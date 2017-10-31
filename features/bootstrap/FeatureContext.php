<?php
use Drupal\draco_dfp\Entity\RegistryFile;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
/**
 * Defines application features used by all features.
 */
class FeatureContext extends RawDrupalContext implements SnippetAcceptingContext {
    /**
     * Install Draco Runsheet module.
     *
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope) {
        /** @var \Drupal\Core\Extension\ModuleHandler $moduleHandler */
        $moduleHandler = \Drupal::service('module_handler');
        \Drupal::service('module_installer')->install(['draco_runsheet']);
    }
    /**
     * Removes sample Runsheet scheduled items.
     *
     * @AfterScenario
     */
    public function cleanupRunsheetScheduledItems() {
        $storage = \Drupal::entityTypeManager()->getStorage('schedule_card');
        $ids = $storage->getQuery()->condition('runsheet.target_id', 'bdd_', 'STARTS_WITH')->execute();
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
    }
    /**
     * Remove sample Runsheet teasers.
     *
     * @AfterScenario
     */
    public function cleanupRunsheetTeasers() {
        $storage = \Drupal::entityTypeManager()->getStorage('runsheet_teaser');
        $ids = $storage->getQuery()->condition('title', 'BDD', 'STARTS_WITH')->execute();
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
    }
    /**
     * Remove sample runsheet teaser bundles.
     *
     * @AfterScenario
     */
    public function cleanupRunsheetTeaserBundles() {
        $storage = \Drupal::entityTypeManager()->getStorage('runsheet_teaser_bundle');
        $ids = $storage->getQuery()->condition('id', 'bdd_', 'STARTS_WITH')->execute();
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
    }
    /**
     * Removes sample Runsheets.
     *
     * @AfterScenario
     */
    public function cleanupRunsheets() {
        $storage = \Drupal::entityTypeManager()->getStorage('runsheet');
        $ids = $storage->getQuery()->condition('id', 'bdd_', 'STARTS_WITH')->execute();
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
    }
}