<?php

namespace App\Services;

use App\Models\CustomerBackup;
use App\Models\CustomerData;
use App\Models\CustomerRegister;
use App\Models\CustomerStockAccess;
use App\Models\CustomerWhatsapp;
use App\Models\Einvoice;
use App\Models\User;
use App\Traits\HashIds;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CustomerService
{
   use HashIds;
   
   public function getCustomers($params) {
      $query = CustomerData::query();
      if(isset($params["quickFilter"]) && $params["quickFilter"]) {
         $keyword = $params["quickFilter"];
         // $query->where('subdesc',$params["quickFilter"]);
         foreach(CustomerData::SEARCHABLE as $field) {
            if($field == 'address') {
               $query->orWhere(DB::raw('concat(COALESCE(subadd1, ""), " " , COALESCE(subadd2,""), " ", COALESCE(subadd3,""))'), 'LIKE', "%".$keyword."%");
               continue;
            }

            if($field == "installdate") {
               $query->orWhere(DB::raw("DATE_FORMAT(installdate,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
               continue;
            }

            if($field == "nextamcdate") {
               $query->orWhere(DB::raw("DATE_FORMAT(nextamcdate,'%d/%m/%Y')"), 'LIKE', "%".$keyword."%");
               continue;
            }

            $query->orWhere($field, 'LIKE', '%'.$keyword.'%');
         }
      }

      if(isset($params['sortOrder']) && $params['sortOrder']) {
         $sortOrder = json_decode($params['sortOrder'], true);
         $query->orderBy($sortOrder['field'], $sortOrder['sort']);
      }

      return $query->with(['eInvoice', 'customerBackup', 'customerStockAccess', 'customerWhatsapp'])->paginate($params["pageSize"], ['*'], 'page', $params['page']);
   }

   public function updateAction($actionType, $acctno, $sourceId, $user) {
      $msg = "Customer updated successfully.";
      $userPermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
      $isSuperUser = $user->hasRole(User::SUPER_USER);
      $customer = CustomerData::find($acctno);

      if($actionType == "lock") {
         if($isSuperUser || in_array("customer_master_lock", $userPermissions)) {
            $msg = $this->toggleCustomerLock($customer);
            return $msg;
         } else {
            throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
         }
      }
      
      if($customer->activestatus == "Y") {
         if($actionType == "einvoice") {
            if($isSuperUser || in_array('customer_master_einvoice', $userPermissions)) {
               $msg = $this->toggleCustomerEinvoice($customer);
               return $msg;
            } else {
               throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
            }
         }
         
         if($actionType == "stockaccess") {
            if($isSuperUser || in_array('customer_stockaccess_toggle', $userPermissions)) {
               $msg = $this->toggleCustomerStockAccess($customer);
               return $msg; 
            } else {
               throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
               
            }
         }
         
         if($actionType == "backupaccess") {
            if($isSuperUser || in_array('customer_backup_toggle', $userPermissions)) {
               $msg = $this->toggleCustomerBackupAccess($customer);
               return $msg;
            } else {
               throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
            }
         }

         if($actionType == "customerwhatsappaccess") {
            if($isSuperUser || in_array('customer_whatsapp_toggle', $userPermissions)) {
               $msg = $this->toggleCustomerWhatsappAccess($customer);
               return $msg;
            } else {
               throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
            }
         }

         if($isSuperUser) {
            if($actionType == "einvoicedelete") {
               app(EinvoiceService::class)->deleteEinvoice($sourceId);
               return "Customer E-Invoice deleted successfully.";
            }
   
            if($actionType == "backupdelete") {
               app(CustomerBackupService::class)->deleteBackupAccess($sourceId);
               return "Customer Backup deleted successfully.";
            }
   
            if($actionType == "stockaccessdelete") {
               app(CustomerStockAccessService::class)->deleteStockAccess($sourceId);
               return "Customer Stock Access deleted successfully.";
            }

            if($actionType == "customerwhatsappdelete") {
               app(CustomerWhatsappService::class)->deleteCustomerWhatsappService($sourceId);
               return "Customer whatsapp deleted successfully.";
            }
         } else {
            throw new UnauthorizedException(Response::HTTP_UNAUTHORIZED, 'Action Not authorized');
         }

      } else {
         throw new Exception("Customer is inactive.");
      }

      return $msg;
   }

   public function toggleCustomerLock($customer) {
     $msg = DB::transaction(function() use($customer) {
         $customer->activestatus = $customer->activestatus == "Y" ? "N" : "Y";
         $customer->save();
         $msg = $customer->activestatus == "Y" ? "Customer unlocked successfully" : "Customer locked successfully";
         return $msg;
     });  
     return $msg;
   }

   public function toggleCustomerEinvoice($customer) {
      $msg = DB::transaction(function() use($customer) {
         $eInvoice = Einvoice::where('acctno', $customer->acctno)->first();
         if($eInvoice) {
            $eInvoice->active = $eInvoice->active == 1 ? 0 : 1;
            $eInvoice->save();
            $msg = $eInvoice->active == 1 ? "Customer E-Invoice enabled successfully. " : "Customer E-Invoice disabled successfully.";
        } else {
            // Create with 1 status
            EInvoice::create(['acctno' => $customer->acctno, 'active' => true, 'created_at' => now(), 'updated_at' => null]);
            $msg = "Customer E-Invoice enabled successfully.";
        }
        return $msg;
      });
      return $msg;
   }

   public function toggleCustomerStockAccess($customer) {
      $msg = DB::transaction(function() use($customer) {
         $customerStockAccess = CustomerStockAccess::where('acctno', $customer->acctno)->first();
         if($customerStockAccess) {
            $customerStockAccess->active = $customerStockAccess->active == 1 ? 0 : 1;
            $customerStockAccess->save();
            $msg = $customerStockAccess->active == 1 ? "Customer stock access enabled successfully." : "Customer stock access disabled successfully";
            return $msg;
         }
      });
      return $msg;
   }

   public function toggleCustomerBackupAccess($customer) {
      $msg = DB::transaction(function() use($customer) {
         $customerBackupAccess = CustomerBackup::where('acctno', $customer->acctno)->first();
         if($customerBackupAccess) {
            $customerBackupAccess->active = $customerBackupAccess->active == 1 ? 0 : 1;
            $customerBackupAccess->save();
            $msg = $customerBackupAccess->active == 1 ? "Customer backup enabled successfully." : "Customer backed disabled successfully.";
            return $msg;
         }
      });
      return $msg;
   }

   public function toggleCustomerWhatsappAccess($customer) {
      $msg = DB::transaction(function() use($customer) {
         $customerWhatsapp = CustomerWhatsapp::where('acctno', $customer->acctno)->first();
         if($customerWhatsapp) {
            $customerWhatsapp->active = $customerWhatsapp->active == 1 ? 0 : 1;
            $customerWhatsapp->save();
            $msg = $customerWhatsapp->active == 1 ? "Customer whatsapp access enabled successfully." : "Customer whatsapp access disabled successfully.";
            return $msg;
        } else {
            // Create with 1 status
            CustomerWhatsapp::create(['acctno' => $customer->acctno, 'active' => true, 'created_at' => now(), 'updated_at' => null]);
            return "Customer whatsapp access enabled successfully.";
        }
      });
      return $msg;
   }

   public function getCustomerDashboardDetails($data, $user) {
      $isSuperUser = $user->hasRole(User::SUPER_USER);
      $userPermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray();
      $data = [];
      if($isSuperUser) {
         $data['customer'] = CustomerData::count();
         $data['backup'] = CustomerBackup::count();
         $data['stock'] = CustomerStockAccess::count();
         $data['einvoice'] = Einvoice::count();
         $data['register'] = CustomerRegister::count();
         $data['whatsapp'] = CustomerWhatsapp::count();
      } else {
         if(in_array('customer_master_view', $userPermissions)) 
            $data['customer'] = CustomerData::count();
         
         if(in_array('customer_backup_view', $userPermissions)) 
            $data['customer'] = CustomerBackup::count();
         
         if(in_array('customer_stockaccess_view', $userPermissions)) 
            $data['stock'] = CustomerStockAccess::count();

         if(in_array('customer_einvoice_view', $userPermissions)) 
            $data['einvoice'] = Einvoice::count();
            
         if(in_array('customer_register_view', $userPermissions)) 
            $data['register'] = CustomerRegister::count();

         if(in_array('customer_whatsapp_view', $userPermissions)) {
            $data['whatsapp'] = CustomerWhatsapp::count();
         }
      }
      return $data;
   }
}

