<?php

namespace Tests\Feature;

use App\Models\LogHour;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use function json_decode;
use \Illuminate\Support\Facades\DB;

class SupplierTest extends TestCase
{
    /**
     * In the task we need to calculate amount of hours suppliers are working during last week for marketing.
     * You can use any way you like to do it, but remember, in real life we are about to have 400+ real
     * suppliers.
     *
     * @return void
     */
    public function testCalculateAmountOfHoursDuringTheWeekSuppliersAreWorking()
    {
        $response = $this->get('/api/suppliers');
        $hours = 0;
        // calculate the hours logged on each day and filter the last week
        if (count($response['data']['suppliers']) > 0) {
            $suppliers = $response['data']['suppliers'];
            $supplierIDs = Arr::pluck($suppliers, 'id');
            $startDate = now()->subDays(7);
            $endDate = now();
            $hours = LogHour::whereIn('supplier_id', $supplierIDs)->whereBetween('start_time', [$startDate, $endDate])->sum('total_time');
        }

        $response->assertStatus(200);
        $this->assertEquals(136, $hours,
            "Our suppliers are working X hours per week in total. Please, find out how much they work..");
    }

    /**
     * Save the first supplier from JSON into database.
     * Please, be sure, all asserts pass.
     *
     * After you save supplier in database, in test we apply verifications on the data.
     * On last line of the test second attempt to add the supplier fails. We do not allow to add supplier with the same name.
     */
    public function testSaveSupplierInDatabase()
    {
        // Disable foreign key checks when truncating, then re-enable it
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LogHour::query()->truncate();
        Supplier::query()->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $responseList = $this->get('/api/suppliers');
        $supplier = json_decode($responseList->getContent(), true)['data']['suppliers'][0];
        $response = $this->post('/api/suppliers', $supplier);
        $response->assertStatus(204);

        $this->assertEquals(1, Supplier::query()->count());
        $dbSupplier = Supplier::query()->first();
        $this->assertNotFalse(curl_init($dbSupplier->url));
        $this->assertNotFalse(curl_init($dbSupplier->rules));
        $this->assertGreaterThan(4, strlen($dbSupplier->info));
        $this->assertNotNull($dbSupplier->name);
        $this->assertNotNull($dbSupplier->district);


        $response = $this->post('/api/suppliers', $supplier);
        $response->assertStatus(422);
    }
}
