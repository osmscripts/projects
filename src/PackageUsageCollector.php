<?php

namespace OsmScripts\Projects;

use OsmScripts\Core\Object_;
use OsmScripts\Core\Project;
use OsmScripts\Projects\Hints\UsageHint;

/**
 * Constructor arguments:
 *
 * @property string $project_path
 * @property string $package_regex
 *
 * Computed properties:
 *
 * @property Project $project
 * @property object[]|UsageHint[] $package_usages
 */
class PackageUsageCollector extends Object_
{
    #region Properties
    protected function default($property) {
        switch ($property) {
            case 'project': return new Project(['path' => $this->project_path]);
            case 'package_usages': return $this->getPackageUsages();
        }

        return parent::default($property);
    }

    protected function getPackageUsages() {
        $result = [];

        foreach ($this->project->packages as $package) {
            if ($this->package_regex && !preg_match(
                "'{$this->package_regex}'", $package->name))
            {
                continue;
            }

            $result[] = (object)[
                'project' => $this->project_path,
                'package' => $package->name,
                'version' => $package->lock->version,
                'constraints' => implode(',', $this->getPackageConstraints($package->name)),
            ];
        }

        return $result;
    }
    #endregion

    /**
     * @param string $packageName
     *
     * @return string[]
     */
    protected function getPackageConstraints($packageName) {
        $result = [];

        foreach ($this->project->packages as $package) {
            if (!is_file("{$this->project_path}/{$package->path}/composer.json")) {
                continue;
            }

            foreach (['require', 'require-dev'] as $section) {
                if ($constraint = $package->json->$section->$packageName ?? null) {
                    $result[$constraint] = true;
                }
            }
        }

        foreach (['require', 'require-dev'] as $section) {
            if ($constraint = $this->project->json->$section->$packageName ?? null) {
                $result[$constraint] = true;
            }
        }

        return array_keys($result);
    }
}