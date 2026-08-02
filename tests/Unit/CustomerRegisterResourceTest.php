<?php

namespace Tests\Unit;

use App\Http\Resources\API\Customer\CustomerRegisterResource;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CustomerRegisterResourceTest extends TestCase
{
    public function test_it_formats_present_and_missing_amc_dates(): void
    {
        $paginator = new LengthAwarePaginator([
            [
                'id' => 1,
                'acctno' => 1001,
                'subdesc' => 'Customer One',
                'datetimereg' => '2026-08-02 14:30:00',
                'next_amc_date' => '2027-04-15',
                'remarks' => 'Registered',
            ],
            [
                'id' => 2,
                'acctno' => 1002,
                'subdesc' => 'Removed Customer',
                'datetimereg' => null,
                'next_amc_date' => null,
                'remarks' => null,
            ],
        ], 2, 10);

        $data = (new CustomerRegisterResource($paginator))->toArray(Request::create('/'));

        $this->assertSame('02/08/2026, 2:30 PM', $data['data'][0]['datetimereg']);
        $this->assertSame('15/04/2027', $data['data'][0]['next_amc_date']);
        $this->assertNull($data['data'][1]['next_amc_date']);
    }
}
