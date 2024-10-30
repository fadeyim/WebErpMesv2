<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LeadsSeeder;
use App\Models\Companies\Companies;
use Database\Seeders\AllocationSeeder;
use Database\Seeders\CreateTaskSeeder;
use Database\Seeders\OrderTableSeeder;
use Database\Seeders\QuotesTableSeeder;
use Database\Seeders\OpportunitiesSeeder;
use App\Models\Companies\CompaniesContacts;
use Database\Seeders\CreateAdminUserSeeder;
use Database\Seeders\MethodsFamiliesSeeder;
use Database\Seeders\OrderLinesTableSeeder;
use Database\Seeders\PermissionTableSeeder;
use Database\Seeders\QuoteLinesTableSeeder;
use App\Models\Companies\CompaniesAddresses;
use Database\Seeders\EstimatedBudgetsSeeder;
use Database\Seeders\MethodsUnitTableSeeder;
use App\Models\Accounting\AccountingDelivery;
use Database\Seeders\MethodsRessourcesSeeder;
use Database\Seeders\MethodsSectionTableSeeder;
use Database\Seeders\MethodsServicesTableSeeder;
use App\Models\Accounting\AccountingPaymentMethod;
use App\Models\Accounting\AccountingPaymentConditions;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**Some table depends on other table. Follow the attached order to seed data
         * 
         */
        //Methodes informations 
        $this->call([
            MethodsServicesTableSeeder::class,
            MethodsUnitTableSeeder::class,
            MethodsSectionTableSeeder::class,
            MethodsRessourcesSeeder::class,
            MethodsFamiliesSeeder::class,
            PermissionTableSeeder::class,
            CreateAdminUserSeeder::class,
            AllocationSeeder::class,
        ]);

        //factory
        $this->call(EstimatedBudgetsSeeder::class);

        //companies informations
        Companies::factory()->count(50)->create();
        CompaniesContacts::factory()->count(200)->create();
        CompaniesAddresses::factory()->count(200)->create();
        //Accounting informations 
        //AccountingVat::factory()->count(4)->create();
        
        AccountingPaymentConditions::factory()->count(5)->create();
        AccountingPaymentMethod::factory()->count(3)->create();
        AccountingDelivery::factory()->count(3)->create();

        
        /*
        *Ressources  seeder
        *Location  seeder
        */

        //Products 
        /*          
        *Products  seeder
        */

        //Leads
        $this->call(LeadsSeeder::class);
        //Oppotunities 
        $this->call(OpportunitiesSeeder::class);
        //Quotes
        $this->call(QuotesTableSeeder::class);
        $this->call(QuoteLinesTableSeeder::class);
        //Orders 
        $this->call(OrderTableSeeder::class);
        $this->call(OrderLinesTableSeeder::class);

        //Task
        $this->call(CreateTaskSeeder::class);
    }
}
