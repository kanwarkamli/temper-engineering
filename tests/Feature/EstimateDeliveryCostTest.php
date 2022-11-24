<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstimateDeliveryCostTest extends TestCase
{
    public function test_command_is_success()
    {
        $this
            ->artisan('delivery:cost')
            ->expectsQuestion('Enter the base delivery cost', 100)
            ->expectsQuestion('Enter number of package(s)', 1)
            ->expectsConfirmation('You have entered the base delivery cost: 100 and number of package(s): 1', 'yes')
            ->expectsQuestion('Enter [PKG1] weight in KG', 5)
            ->expectsQuestion('Enter [PKG1] distance in KM', 5)
            ->expectsQuestion('Enter [PKG1] offer code (if applicable)', '')
            ->expectsTable(['Package', 'Discount', 'Estimate Cost'], [['PKG1', 0, 175]])
            ->assertSuccessful();
    }

    public function test_command_return_error_with_invalid_input()
    {
        $this
            ->artisan('delivery:cost')
            ->expectsQuestion('Enter the base delivery cost', 'abc')
            ->expectsOutput('The base delivery cost must be a number.')
            ->expectsQuestion('Enter the base delivery cost', 100)
            ->expectsQuestion('Enter number of package(s)', 'abc')
            ->expectsOutput('The number of package must be a number.')
            ->expectsQuestion('Enter number of package(s)', 1)
            ->expectsConfirmation('You have entered the base delivery cost: 100 and number of package(s): 1', 'yes')
            ->expectsQuestion('Enter [PKG1] weight in KG', 'abc')
            ->expectsOutput('The package weight must be a number.')
            ->expectsQuestion('Enter [PKG1] weight in KG', 5)
            ->expectsQuestion('Enter [PKG1] distance in KM', 'abc')
            ->expectsOutput('The package distance must be a number.')
            ->expectsQuestion('Enter [PKG1] distance in KM', 5)
            ->expectsQuestion('Enter [PKG1] offer code (if applicable)', '')
            ->expectsTable(['Package', 'Discount', 'Estimate Cost'], [['PKG1', 0, 175]])
            ->assertSuccessful();
    }

    public function test_command_with_valid_offer_code()
    {
        $this
            ->artisan('delivery:cost')
            ->expectsQuestion('Enter the base delivery cost', 100)
            ->expectsQuestion('Enter number of package(s)', 1)
            ->expectsConfirmation('You have entered the base delivery cost: 100 and number of package(s): 1', 'yes')
            ->expectsQuestion('Enter [PKG1] weight in KG', 10)
            ->expectsQuestion('Enter [PKG1] distance in KM', 100)
            ->expectsQuestion('Enter [PKG1] offer code (if applicable)', 'ofr003')
            ->expectsTable(['Package', 'Discount', 'Estimate Cost'], [['PKG1', 35, 665]])
            ->assertSuccessful();
    }

    public function test_command_with_invalid_offer_code()
    {
        $this
            ->artisan('delivery:cost')
            ->expectsQuestion('Enter the base delivery cost', 100)
            ->expectsQuestion('Enter number of package(s)', 1)
            ->expectsConfirmation('You have entered the base delivery cost: 100 and number of package(s): 1', 'yes')
            ->expectsQuestion('Enter [PKG1] weight in KG', 10)
            ->expectsQuestion('Enter [PKG1] distance in KM', 100)
            ->expectsQuestion('Enter [PKG1] offer code (if applicable)', 'randomcode')
            ->expectsTable(['Package', 'Discount', 'Estimate Cost'], [['PKG1', 0, 700]])
            ->assertSuccessful();
    }

}
