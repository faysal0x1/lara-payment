<?php

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Faysal0x1\LaraPayment\Contracts\PaystackContract;

it('can instantiate PaystackContract instance', function () {
    expect($this->paystack)
        ->toBeObject()
        ->toBeInstanceOf(PaystackContract::class);
});

it('can redirect to checkout for payment using passed arguments', function () {
    $this->paystack
        ->shouldReceive('redirectToCheckout')
        ->once()
        ->with([
            'amount' => 1000,
            'email' => 'faysal0x1@test.com',
            'reference' => '123456789',
            'callback_url' => 'https://example.com',
        ])
        ->andReturnValues([
            new RedirectResponse('https://checkout.paystack.com/123456789'),
            [
                'status' => true,
                'message' => 'Checkout URL generated',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/123456789',
                    'access_code' => '123456789',
                    'reference' => '123456789',
                ],
            ],
        ])
        ->andReturn(new RedirectResponse('https://checkout.paystack.com/123456789'));

    expect($this->paystack->redirectToCheckout([
        'amount' => 1000,
        'email' => 'faysal0x1@test.com',
        'reference' => '123456789',
        'callback_url' => 'https://example.com',
    ]))
        ->toBeObject()
        ->toBeInstanceOf(RedirectResponse::class);
});

it('can redirect to checkout for payment using request() arguments', function () {
    $this->paystack
        ->shouldReceive('redirectToCheckout')
        ->once()
        ->andReturnValues([
            new RedirectResponse('https://checkout.paystack.com/123456789'),
            [
                'status' => true,
                'message' => 'Checkout URL generated',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/123456789',
                    'access_code' => '123456789',
                    'reference' => '123456789',
                ],
            ],
        ])
        ->andReturn(new RedirectResponse('https://checkout.paystack.com/123456789'));

    expect($this->paystack->redirectToCheckout())
        ->toBeObject()
        ->toBeInstanceOf(RedirectResponse::class);
});

it('can get the list of all banks', function () {
    $this->paystack
        ->shouldReceive('getBanks')
        ->once()
        ->andReturn([
            'status' => true,
            'message' => 'Banks retrieved',
            'data' => ['*'],
        ]);

    expect($this->paystack->getBanks())
        ->toBeArray()
        ->toBe([
            'status' => true,
            'message' => 'Banks retrieved',
            'data' => ['*'],
        ]);
});

//it('can make fake http request to get list of banks', function () {
//    $body = file_get_contents(__DIR__.'/Fixtures/banks.json');
//
//    Http::fake([
//        'https://api.paystack.co/bank' => Http::response($body, 200),
//    ]);
//
//    // assert if there is a bank with name "Abbey Mortgage Bank" in the list of banks
////    $body =
//
//
////    expect(json_decode($body, true)['data'])
////        ->toBeArray()
//////        ->dd()
////        ->toContain(fn ($bank) => $bank['name'] === 'Abbey Mortgage Bank');
//
////    expect(
////        collect(json_decode($body, true)['data'])
////            ->where('name', 'Abbey Mortgage Bank')
////            ->isNotEmpty()
////    )
////        ->toBeTrue();
//
//
//
////    expect($body)
////        ->toBeString()
////        ->toContain('Abbey Mortgage Bank');
//
////    expect(json_decode($body, true))
////        ->toBeArray()
////        ->toHaveKeys([
////            'status',
////            'message',
////            'data',
////        ])
////        ->toContain('data');
//});
