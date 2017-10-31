<?php
use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
/**
 * Defines application features for runsheet.feature.
 */
class RunsheetContext extends RawDrupalContext implements SnippetAcceptingContext {
    /**
     * @When I create a Runsheet
     */
    public function iCreateARunsheet() {
        $this->createRunsheet();
    }
    /**
     * @When I create a Runsheet named :name
     */
    public function iCreateARunsheetNamed($name) {
        $this->createRunsheet($name);
    }
    /**
     * @Then I should be able to delete it
     */
    public function iShouldBeAbleToDeleteIt() {
        $this->visitPath('/admin/structure/runsheet/bdd_sample_runsheet/delete');
        $this->getSession()->getPage()->pressButton('Delete');
        $this->assertSession()->pageTextContains('content runsheet: deleted BDD Sample runsheet');
    }
    /**
     * @When I open the form to create a Runsheet
     */
    public function iOpenTheFormToCreateARunsheet() {
        $this->visitPath('admin/structure/runsheet/add');
    }
    /**
     * @When submit the form with duplicated positions
     */
    public function submitTheFormWithDuplicatedPositions() {
        $label_field = $this->getSession()->getPage()->findField('label');
        // Set the value for the field, triggering the machine name update.
        $label_field->setValue('BDD Sample runsheet');
        // Wait the set timeout for fetching the machine name.
        $this->getSession()->wait(1000, 'jQuery("#edit-label-machine-name-suffix .machine-name-value").html() == "bdd_sample_runsheet"');
        // Click on Add position to render an extra set of position fields.
        $this->getSession()->getPage()->pressButton('Add position');
        $this->getSession()->wait(2000, 'jQuery(\'input[name="positions[1][label]"]\').length === 1');
        // Fill out position fields duplicating the id.
        $page = $this->getSession()->getPage();
        $page->fillField('positions[0][label]', 'Position 1');
        $page->fillField('positions[0][id]', 'position_1');
        $page->fillField('positions[1][label]', 'Position 2');
        $page->fillField('positions[1][id]', 'position_1');
        $page->pressButton('Save');
    }
    /**
     * @Then I should see an error saying that they must be unique
     */
    public function iShouldSeeAnErrorSayingThatTheyMustBeUnique() {
        $this->assertSession()->pageTextContains('The machine-readable name position_1 is already in use. It must be unique.');
    }
    /**
     * @When I fix validation errors but I introduce a new element with duplicated id
     */
    public function iFixValidationErrorsButIIntroduceANewElementWithDuplicatedId() {
        $this->getSession()->getPage()->pressButton('Add position');
        $this->getSession()->wait(2000, 'jQuery(\'input[name="positions[2][label]"]\').length === 1');
        $page = $this->getSession()->getPage();
        $page->fillField('positions[2][label]', 'Position 3');
        $page->fillField('positions[2][id]', 'position_3');
        $page->pressButton('Save');
    }
    /**
     * @When I request a new position field
     */
    public function iRequestANewPositionField() {
        $this->getSession()->getPage()->pressButton('Add position');
        $this->getSession()->wait(2000, 'jQuery(\'input[name="delete_position_1"]\').length');
    }
    /**
     * @When I delete the new position field
     */
    public function iDeleteTheNewPositionField() {
        $this->getSession()->getPage()->pressButton('Delete position');
    }
    /**
     * @When I fill the rest of the fields and submit
     */
    public function iFillTheRestOfTheFieldsAndSubmit() {
        $page = $this->getSession()->getPage();
        $page->fillField('label', 'BDD Sample Runsheet');
        // Wait the set timeout for fetching the machine name.
        $this->getSession()->wait(1000, 'jQuery("#edit-label-machine-name-suffix .machine-name-value").html() == "bdd_sample_runsheet"');
        $page->fillField('positions[0][label]', 'Position 1');
        $page->fillField('positions[0][id]', 'position_1');
        $this->getSession()->getPage()->pressButton('Save');
    }
    /**
     * @Then I should see a success message
     */
    public function iShouldSeeASuccessMessage() {
        $this->assertSession()->pageTextContains('Created the BDD Sample Runsheet Runsheet.');
    }
    /**
     * @When I add a Runsheet teaser bundle named :name
     */
    public function iAddRunsheetTeaserBundleNamed($name) {
        $name = 'BDD ' . $name;
        $machine_name = $this->getMachineName($name);
        $this->visitPath('admin/structure/runsheet_teaser_bundle/add');
        // Fill label field.
        $page = $this->getSession()->getPage();
        $page->fillField('label', $name);
        // Wait the set timeout for fetching the machine name.
        $this->getSession()->wait(1000, 'jQuery("#edit-label-machine-name-suffix .machine-name-value").html() == "' . $machine_name . '"');
        $page->pressButton('Save');
    }
    /**
     * @When I create a :bundle Runsheet teaser named :name
     */
    public function iCreateBundleRunsheetTeaser($bundle, $name) {
        $bundle = 'BDD ' . $bundle;
        $machine_name = $this->getMachineName($bundle);
        $this->visitPath('admin/runsheet_teaser/add/' . $machine_name);
        // Fill title field.
        $page = $this->getSession()->getPage();
        $page->fillField('title[0][value]', 'BDD ' . $name);
        $page->pressButton('Save');
    }
    /**
     * @When I create a scheduled runsheet item for item :teaser_name on runsheet :runsheet_name
     */
    public function iCreateScheduledRunsheetItemForTeaserOnRunsheet($teaser_name, $runsheet_name) {
        $teaser_name = 'BDD ' . $teaser_name;
        $runsheet_machine_name = $this->getMachineName('BDD ' . $runsheet_name);
        /** @var \Drupal\Core\Entity\EntityStorageInterface $storage */
        $storage = \Drupal::service('entity_type.manager')->getStorage('runsheet_teaser');
        $query = $storage->getQuery()
            ->condition('title', $teaser_name);
        if ($ids = $query->execute()) {
            $teasers = $storage->loadMultiple($ids);
            $teaser = reset($teasers);
            $teaser_id = $teaser->id();
            // At this point we have all we need to create a scheduled item, let's do
            // it.
            $this->visitPath('admin/schedule-item/add');
            // Fill in the fields.
            $page = $this->getSession()->getPage();
            $page->fillField('card_id[0][target_id]', sprintf('%s (%s)', $teaser_name, $teaser_id));
            $page->fillField('runsheet[0][target_id]', $runsheet_machine_name);
            // Wait for the position selector to show up.
            $this->getSession()->wait(1000, 'jQuery(\'[data-drupal-selector=edit-runsheet-0-runsheet-position]\').length == 1');
            $page->fillField('runsheet[0][runsheet_position]', 'position_1');
            $page->fillField('start_date[0][value][date]', '2017-04-12');
            $this->getSession()->evaluateScript('jQuery(\'#edit-start-date-0-value-date\').val(\'2017-04-12\');');
            $this->getSession()->evaluateScript('jQuery(\'#edit-start-date-0-value-time\').val(\'11:11:11\');');
            $this->getSession()->evaluateScript('jQuery(\'#edit-end-date-0-value-date\').val(\'2017-04-12\');');
            $this->getSession()->evaluateScript('jQuery(\'#edit-end-date-0-value-time\').val(\'14:22:22\');');
            $page->pressButton('Save');
        }
    }
    /**
     * @When I filter the scheduled runsheeet items by :name
     */
    public function iFilterScheduledRunsheetItemsByName($name) {
        $runsheet_machine_name = $this->getMachineName('BDD ' . $name);
        $this->visitPath('admin/content/scheduled-items?rs=' . $runsheet_machine_name);
    }
    /**
     * @Then I should see :count scheduled runsheet item
     */
    public function iShouldSeeSindleScheduledRunsheetItem($count) {
        $this->assertSession()->elementsCount('css', 'tbody tr', $count);
    }
    /**
     * Helper function to create a runsheet with a given name.
     *
     * @param string $name
     *   Name of the runsheet to create.
     */
    protected function createRunsheet($name = NULL) {
        if (!$name) {
            $name = 'Sample runsheet';
        }
        $name = 'BDD ' . $name;
        $machine_name = $this->getMachineName($name);
        // Create a runsheet.
        $this->visitPath('admin/structure/runsheet/add');
        $page = $this->getSession()->getPage();
        $label_field = $page->findField('label');
        // Set the value for the field, triggering the machine name update.
        $label_field->setValue($name);
        // Wait the set timeout for fetching the machine name.
        $this->getSession()
            ->wait(1000, 'jQuery("#edit-label-machine-name-suffix .machine-name-value").html() == "' . $machine_name . '"');
        // Fill out the other required fields.
        $page->fillField('positions[0][label]', 'Position 1');
        $page->fillField('positions[0][id]', 'position_1');
        $page->pressButton('Save');
        $this->assertSession()->pageTextContains('Created the ' . $name . ' Runsheet.');
    }
    /**
     * Helper function to turn a name into a machine name.
     *
     * Uses the same rules Drupal core uses.
     *
     * @param string $name
     *   Proper name to turn into a machine name.
     *
     * @return string
     *   Machine name.
     */
    protected function getMachineName($name) {
        // Pattern gleaned from
        // \Drupal\Core\Render\Element\MachineName\processMachineName.
        return preg_replace('/[^a-z0-9_]+/', '_', strtolower($name));
    }
}