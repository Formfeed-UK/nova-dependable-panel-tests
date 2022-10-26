<?php

namespace Tests\Browser;

use App\Models\Test;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Contracts\Console\Kernel;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

use Laravel\Nova\Testing\Browser\Pages\Create;
use Laravel\Nova\Testing\Browser\Pages\Detail;

class CreationTestDefaults extends DuskTestCase {

    //use DatabaseMigrations;
    /**
     * A basic browser test example.
     *
     * @return void
     */

    public function runDatabaseMigrations() {
        $this->artisan('migrate:fresh', ['--seed' => true]);

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback');
            RefreshDatabaseState::$migrated = false;
        });
    }

    public function testDefaultExists() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test1_field1")
                ->waitFor("@record_data->test1_field2")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_default")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_default");
        });
    }

    public function testDefaultExistsAfterPanelVisibilityToggle() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test1_field1")
                ->waitFor("@record_data->test1_field2")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_default")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_default")
                ->check(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-1")
                ->assertMissing("@record_data->test1_field1")
                ->assertMissing("@record_data->test1_field2");

            $browser->uncheck(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-1")
                ->assertVisible("@record_data->test1_field1")
                ->assertVisible("@record_data->test1_field2")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_default")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_default");
        });
    }

    public function testUpdatedInputAfterVisibilityToggle() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test1_field1")
                ->waitFor("@record_data->test1_field2")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_default")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_default")
                ->type("@record_data->test1_field1", "test1_field1_updated")
                ->type("@record_data->test1_field2", "test1_field2_updated")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_updated")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_updated")
                ->check(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertMissing(".component-form-nova-dependable-panel.field-test-1")
                ->assertMissing("@record_data->test1_field1")
                ->assertMissing("@record_data->test1_field2");

            $browser->uncheck(".field-record-data-test-1-boolean input")
                ->pause(500)
                ->assertVisible(".component-form-nova-dependable-panel.field-test-1")
                ->assertVisible("@record_data->test1_field1")
                ->assertVisible("@record_data->test1_field2")
                ->assertInputValue("@record_data->test1_field1", "test1_field1_updated")
                ->assertInputValue("@record_data->test1_field2", "test1_field2_updated");
        });
    }

    public function testSingleRequestDefaults() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test2_field1")
                ->waitFor("@record_data->test2_field2")
                ->assertInputValue("@record_data->test2_field1", "test2_field1_default")
                ->assertInputValue("@record_data->test2_field2", "test2_field2_default")
                ->check(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertNotPresent("@record_data->test2_field1")
                ->assertNotPresent("@record_data->test2_field2")
                ->assertPresent("@record_data->test2_field3")
                ->assertInputValue("@record_data->test2_field3", "test2_field3_default")
                ->uncheck(".field-record-data-test-2-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test2_field1")
                ->assertPresent("@record_data->test2_field2")
                ->assertInputValue("@record_data->test2_field1", "test2_field1_default")
                ->assertInputValue("@record_data->test2_field2", "test2_field2_default");
        });
    }

    public function testMultipleRequestDefaults() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test3_field1")
                ->waitFor("@record_data->test3_field2")
                ->assertInputValue("@record_data->test3_field1", "test3_field1_default")
                ->assertInputValue("@record_data->test3_field2", "test3_field2_default")
                ->check(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertNotPresent("@record_data->test3_field1")
                ->assertNotPresent("@record_data->test3_field2")
                ->assertPresent("@record_data->test3_field3")
                ->assertInputValue("@record_data->test3_field3", "test3_field3_default")
                ->uncheck(".field-record-data-test-3-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test3_field1")
                ->assertPresent("@record_data->test3_field2")
                ->assertInputValue("@record_data->test3_field1", "test3_field1_default")
                ->assertInputValue("@record_data->test3_field2", "test3_field2_default");
        });
    }

    public function testValueUpdates() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test4_field1")
                ->assertInputValue("@record_data->test4_field1", "test4_field1_default")
                ->check(".field-record-data-test-4-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test4_field1")
                ->assertInputValue("@record_data->test4_field1", "test4_field1_changed")
                ->uncheck(".field-record-data-test-4-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test4_field1", "test4_field1_changed");

            $browser
                ->waitFor("@record_data->test5_field1")
                ->assertInputValue("@record_data->test5_field1", "test5_field1_default")
                ->check(".field-record-data-test-5-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test5_field1")
                ->assertInputValue("@record_data->test5_field1", "test5_field1_changed")
                ->uncheck(".field-record-data-test-5-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test5_field1", "test5_field1_changed");
        });
    }

    public function testApplyToFieldsUpdates() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test6_field1")
                ->waitFor("@record_data->test6_field2")
                ->assertInputValue("@record_data->test6_field1", "test6_field1_default")
                ->assertInputValue("@record_data->test6_field2", "test6_field2_default")
                ->check(".field-record-data-test-6-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test6_field1", "test6_general_updated")
                ->assertInputValue("@record_data->test6_field2", "test6_field2_updated")
                ->uncheck(".field-record-data-test-6-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test6_field1", "test6_general_updated")
                ->assertInputValue("@record_data->test6_field2", "test6_field2_updated");

                $browser
                ->waitFor("@record_data->test7_field1")
                ->waitFor("@record_data->test7_field2")
                ->assertInputValue("@record_data->test7_field1", "test7_field1_default")
                ->assertInputValue("@record_data->test7_field2", "test7_field2_default")
                ->check(".field-record-data-test-7-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test7_field1", "test7_general_updated")
                ->assertInputValue("@record_data->test7_field2", "test7_field2_updated")
                ->uncheck(".field-record-data-test-7-boolean input")
                ->pause(500)
                ->assertInputValue("@record_data->test7_field1", "test7_general_updated")
                ->assertInputValue("@record_data->test7_field2", "test7_field2_updated");
        });
    }

    public function testAdditionalFieldsHaveDefaults() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test8_field1")
                ->waitFor("@record_data->test8_field2")
                ->assertInputValue("@record_data->test8_field1", "test8_field1_default")
                ->assertInputValue("@record_data->test8_field2", "test8_field2_default")
                ->type("@record_data->test8_field1", "test8_field1_changed")
                ->type("@record_data->test8_field2", "test8_field2_changed")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field3")
                ->assertPresent("@record_data->test8_field4")
                ->assertInputValue("@record_data->test8_field3", "test8_field3_default")
                ->assertInputValue("@record_data->test8_field4", "test8_field4_default")
                ->type("@record_data->test8_field3", "test8_field3_changed")
                ->type("@record_data->test8_field4", "test8_field4_changed")
                ->uncheck(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field1")
                ->assertPresent("@record_data->test8_field2")
                ->assertInputValue("@record_data->test8_field1", "test8_field1_changed")
                ->assertInputValue("@record_data->test8_field2", "test8_field2_changed")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field3")
                ->assertPresent("@record_data->test8_field4")
                ->assertInputValue("@record_data->test8_field3", "test8_field3_changed")
                ->assertInputValue("@record_data->test8_field4", "test8_field4_changed");
        });
    }

    public function testDefaultsNotAppliedOnSubmit() {
        $this->closeAll();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(1)
                ->visit(new Create('test-creation-defaults'))
                ->waitFor("@record_data->test8_field1")
                ->waitFor("@record_data->test8_field2")
                ->assertInputValue("@record_data->test8_field1", "test8_field1_default")
                ->assertInputValue("@record_data->test8_field2", "test8_field2_default")
                ->type("@record_data->test8_field1", "test8_field1_changed")
                ->type("@record_data->test8_field2", "test8_field2_changed")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field3")
                ->assertPresent("@record_data->test8_field4")
                ->assertInputValue("@record_data->test8_field3", "test8_field3_default")
                ->assertInputValue("@record_data->test8_field4", "test8_field4_default")
                ->type("@record_data->test8_field3", "test8_field3_changed")
                ->type("@record_data->test8_field4", "test8_field4_changed")
                ->uncheck(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field1")
                ->assertPresent("@record_data->test8_field2")
                ->assertInputValue("@record_data->test8_field1", "test8_field1_changed")
                ->assertInputValue("@record_data->test8_field2", "test8_field2_changed")
                ->check(".field-record-data-test-8-boolean input")
                ->pause(500)
                ->assertPresent("@record_data->test8_field3")
                ->assertPresent("@record_data->test8_field4")
                ->assertInputValue("@record_data->test8_field3", "test8_field3_changed")
                ->assertInputValue("@record_data->test8_field4", "test8_field4_changed")
                ->create()
                ->waitForText("The test creation default was created");

            $test = Test::orderBy('id', 'desc')->first();

            $this->assertSame(true, $test->record_data['test8_boolean']);
            $this->assertThat($test->record_data, $this->logicalNot($this->arrayHasKey('test8_field1')));
            $this->assertThat($test->record_data, $this->logicalNot($this->arrayHasKey('test8_field2')));
            $this->assertSame("test8_field3_changed", $test->record_data['test8_field3']);
            $this->assertSame("test8_field4_changed", $test->record_data['test8_field4']);

        });
    }


}
