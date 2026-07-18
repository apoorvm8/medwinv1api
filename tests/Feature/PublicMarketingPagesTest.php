<?php

namespace Tests\Feature;

use Tests\TestCase;

class PublicMarketingPagesTest extends TestCase
{
    public function test_homepage_renders_the_new_marketing_experience(): void
    {
        $response = $this->get(route('index'));

        $response
            ->assertOk()
            ->assertSee('Pharmacy Billing,')
            ->assertSee('Inventory and Accounts')
            ->assertSee('Made Simple.')
            ->assertSee('1,500+ satisfied customers')
            ->assertSee('Mr. Shrawan Jaiswal')
            ->assertSee('id="solutions"', false)
            ->assertSee('id="features"', false)
            ->assertSee('id="contact"', false)
            ->assertSee('class="windows-laptop"', false)
            ->assertSee('hero-windows-laptop', false)
            ->assertSee('assets/img/pharmacy_soft0.jpg', false)
            ->assertSee('assets/img/purchase-screen.png', false)
            ->assertSee('IntersectionObserver')
            ->assertSee('Windows');
    }

    public function test_shared_customer_login_contract_is_present(): void
    {
        $response = $this->get(route('index'));

        $response
            ->assertOk()
            ->assertSee('id="customerLoginModal"', false)
            ->assertSee('id="customerLoginForm"', false)
            ->assertSee('name="customer_id"', false)
            ->assertSee('name="password"', false)
            ->assertSee('id="customerLoginBtn"', false);
    }

    public function test_contact_page_preserves_enquiry_fields(): void
    {
        $response = $this->get(route('contact', ['type_of_soft' => 'retailPharma']));

        $response
            ->assertOk()
            ->assertSee('id="contactForm"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="mobile_no"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="type_of_soft"', false)
            ->assertSee('value="retailPharma" selected', false);
    }

    public function test_contact_submission_keeps_required_validation(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $response = $this->from(route('contact'))->post(route('customer.msg.submit'), []);

        $response
            ->assertRedirect(route('contact'))
            ->assertSessionHasErrors(['name', 'mobile_no', 'type_of_soft']);
    }

    public function test_public_product_pages_render(): void
    {
        $routes = ['pharmacy', 'bookstore', 'departmental', 'footwear'];

        foreach ($routes as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('class="product-hero', false)
                ->assertSee('container site-container', false)
                ->assertSee('product-process-heading', false)
                ->assertSee('Request a demo');
        }
    }

    public function test_private_legacy_product_screens_are_not_exposed(): void
    {
        $privateScreens = [
            'bookstore' => 'assets/img/bookstore_soft0.jpg',
            'departmental' => 'assets/img/grocery_soft0.jpg',
            'footwear' => 'assets/img/footwear_soft0.jpg',
        ];

        foreach ($privateScreens as $route => $screen) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee('Illustrative workflow preview with demo data')
                ->assertDontSee($screen, false);
        }

        $this->get(route('pharmacy'))
            ->assertOk()
            ->assertSee('Retail pharmacy software for faster billing and stock control.')
            ->assertSee('product-page-laptop', false)
            ->assertSee('assets/img/pharmacy_soft0.jpg', false)
            ->assertSee('data-lightbox="bill"', false);

        $this->get(route('pharmacy', ['segment' => 'wholesale']))
            ->assertOk()
            ->assertSee('Wholesale pharmacy software for sales, stock and distribution.')
            ->assertSee('assets/img/pharmacy-wholesale-sale-screen.png', false)
            ->assertSee('product-page-laptop__image--contain', false)
            ->assertDontSee('assets/img/pharmacy_soft0.jpg', false);
    }

    public function test_wholesale_product_link_preserves_enquiry_context(): void
    {
        $this->get(route('pharmacy', ['segment' => 'wholesale']))
            ->assertOk()
            ->assertSee('type_of_soft=wholeSalePharma', false);

        $this->get(route('footwear'))
            ->assertOk()
            ->assertSee('Retail footwear software for faster billing and variant-wise stock.')
            ->assertSee('Footwear counter inventory')
            ->assertSee('type_of_soft=retailFootwear', false);

        $this->get(route('footwear', ['segment' => 'wholesale']))
            ->assertOk()
            ->assertSee('Wholesale footwear software for bulk orders, stock and dispatch.')
            ->assertSee('Party order inventory')
            ->assertSee('type_of_soft=wholeSaleFootwear', false)
            ->assertDontSee('Retail footwear software for faster billing and variant-wise stock.');
    }

    public function test_terms_page_uses_the_public_layout(): void
    {
        $this->get(route('terms_and_conditions'))
            ->assertOk()
            ->assertSee('Terms and Conditions')
            ->assertSee('Customer login');
    }

    public function test_product_previews_do_not_use_macos_window_dots(): void
    {
        $css = file_get_contents(public_path('css/style.css'));

        $this->assertStringNotContainsString('.product-hero .product-screen__bar::before', $css);
    }
}
