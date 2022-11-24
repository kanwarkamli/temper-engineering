<?php

namespace App\Console\Commands;

use App\DeliveryService\DeliveryCost;
use App\Offer\Offer;
use App\Offer\OfferFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class EstimateDeliveryCost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delivery:cost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will estimate the total delivery cost of each package with an offer code (if applicable)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $base_delivery_cost = (float) $this->askAndValidate('Enter the base delivery cost', 'base_delivery_cost', ['required', 'numeric', 'min:1']);
        $number_of_package = (int) $this->askAndValidate('Enter number of package(s)', 'number_of_package', ['required', 'numeric', 'min:1']);

        $result_collections = [];

        $delivery_cost = new DeliveryCost();
        $delivery_cost->setBaseCost($base_delivery_cost);
        $delivery_cost->setNumberOfPackage($number_of_package);

        // Confirm if base cost and number of package are correct, else start back
        if ($this->confirm("You have entered the base delivery cost: $base_delivery_cost and number of package(s): $number_of_package")) {

            for ($i = 1; $i <= $number_of_package; $i++) {
                $this->info("============ Package $i Details [PKG{$i}] ============");

                $package_weight = (float) $this->askAndValidate("Enter [PKG{$i}] weight in KG", 'package_weight', ['required', 'numeric', 'min:1']);
                $package_distance = (float) $this->askAndValidate("Enter [PKG{$i}] distance in KM", 'package_distance', ['required', 'numeric', 'min:1']);
                $package_offer_code = $this->askAndValidate("Enter [PKG{$i}] offer code (if applicable)", 'package_offer_code', ['nullable']);

                $delivery_cost->setWeight($package_weight);
                $delivery_cost->setDistance($package_distance);

                $delivery_cost->initiateOffer($package_offer_code);
                $delivery_cost_total = $delivery_cost->calculate();

                $result_collections[] = [
                    'package_no' => "PKG{$i}",
                    'discount_amount' => $delivery_cost->getDiscountAmount(),
                    'delivery_cost_total' => $delivery_cost_total,
                ];
            }

            $this->resultTable($result_collections);
        }

        return 0;
    }

    /**
     * Display result nicely in a table form
     *
     * @param $data
     * @return void
     */
    private function resultTable($data)
    {
        $headers = [
            'Package',
            'Discount',
            'Estimate Cost',
        ];

        $this->table($headers, $data);
    }

    /**
     * Improvement on the ask() with field validation 'integration'
     *
     * @param string $question
     * @param string $field
     * @param array $rules
     * @return mixed
     */
    private function askAndValidate(string $question, string $field, array $rules)
    {
        $answer = $this->ask($question);

        if ($message = $this->validateInput($rules, $field, $answer)) {
            $this->error($message);

            return $this->askAndValidate($question, $field, $rules);
        }

        return $answer;
    }

    /**
     * Here is where the validation is really done
     *
     * @param array $rules
     * @param string $field
     * @param $answer
     * @return string|null
     */
    private function validateInput(array $rules, string $field, $answer)
    {
        $validator = Validator::make([
            $field => $answer
        ], [
            $field => $rules
        ]);

        return $validator->fails()
            ? $validator->errors()->first($field)
            : null;
    }
}
