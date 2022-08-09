<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

namespace core_reportbuilder\external\filters;

use core_reportbuilder_generator;
use external_api;
use externallib_advanced_testcase;
use core_reportbuilder\local\helpers\user_filter_manager;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests external filters reset class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\filters\reset
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class reset_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);

        user_filter_manager::set($report->get('id'), [
            'entity:filter_name' => 'something',
        ]);

        // Sanity check that we get back the filter we just set.
        $this->assertCount(1, user_filter_manager::get($report->get('id')));

        $result = reset::execute($report->get('id'));
        $result = external_api::clean_returnvalue(reset::execute_returns(), $result);

        $this->assertTrue($result);

        // We should get an empty array back.
        $this->assertEquals([], user_filter_manager::get($report->get('id')));
    }
}
