<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\generis\model\data\Ontology;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;

final class Version202205232110572348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix ontology models.';
    }

    public function up(Schema $schema): void
    {
        $serviceLocator = $this->getServiceLocator();

        /** @var SmoothModel $ontology */
        $ontology = $this->getServiceLocator()->get(Ontology::SERVICE_ID);

        $this->fixModels($ontology, SmoothModel::OPTION_READABLE_MODELS);
        $this->fixModels($ontology, SmoothModel::OPTION_WRITEABLE_MODELS);
        $this->fixModel($ontology, SmoothModel::OPTION_NEW_TRIPLE_MODEL);

        $serviceLocator->register(Ontology::SERVICE_ID, $ontology);
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }

    private function fixModels(Ontology $ontology, string $option): void
    {
        $models = $ontology->getOption($option, []);

        foreach ($models as $index => $model) {
            if (!is_int($model)) {
                $models[$index] = (int) $model;
            }
        }

        $ontology->setOption($option, $models);
    }

    private function fixModel(Ontology $ontology, string $option): void
    {
        $model = $ontology->getOption($option);

        if (!is_int($model)) {
            $model = (int) $model;

            $ontology->setOption($option, $model);
        }
    }
}
