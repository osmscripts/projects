<?php

namespace OsmScripts\Projects\Commands;

use OsmScripts\Projects\Hints\UsageHint;
use OsmScripts\Projects\PackageUsageCollector;
use OsmScripts\Core\Command;
use OsmScripts\Core\Variables;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/** @noinspection PhpUnused */

/**
 * `package:usages` shell command class.
 *
 * Dependencies:
 *
 * @property Variables $variables Helper for managing script variables
 *
 * Command line arguments and options:
 *
 * @property string $project_path
 * @property string $package_regex
 * @property string $by
 *
 * Computed properties:
 *
 * @property object[]|UsageHint[] $package_usages
 * @property array $usages_by_project
 * @property array $usages_by_package
 */
class ShowPackages extends Command
{
    #region Properties
    public function default($property) {
        global $script;

        switch ($property) {
            case 'variables': return $script->singleton(Variables::class);

            case 'project_path': return $this->input->getOption('projects');
            case 'package_regex': return $this->input->getArgument('packages');
            case 'by': return $this->input->getOption('by');

            case 'usages_by_project': return $this->getUsagesByProject();
            case 'usages_by_package': return $this->getUsagesByPackage();
            case 'package_usages': return $this->getPackageUsages();
        }

        return parent::default($property);
    }

    protected function getUsagesByProject() {
        $result = [];

        foreach ($this->package_usages as $usage) {
            $key = $usage->project;

            if (!isset($result[$key])) {
                $result[$key] = [$usage];
            }
            else {
                $result[$key][] = $usage;
            }
        }

        return $result;
    }

    protected function getUsagesByPackage() {
        $result = [];

        foreach ($this->package_usages as $usage) {
            $key = $usage->package;

            if (!isset($result[$key])) {
                $result[$key] = [$usage];
            }
            else {
                $result[$key][] = $usage;
            }
        }

        return $result;
    }

    protected function getPackageUsages() {
        $result = [];

        foreach (explode(';', $this->project_path) as $pattern) {
            if (!$pattern) {
                continue;
            }

            if (!($paths = glob($pattern))) {
                continue;
            }

            foreach ($paths as $path) {
                if (!is_file("{$path}/composer.lock")) {
                    continue;
                }

                $usageCollector = new PackageUsageCollector([
                    'project_path' => $path,
                    'package_regex' => $this->package_regex,
                ]);

                $result = array_merge($result, $usageCollector->package_usages);
            }
        }

        return $result;
    }
    #endregion

    protected function configure() {
        $this
            ->setDescription('Lists requested and actual versions ' .
                'of specified Composer packages in various projects')
            ->addOption('projects', null,
                InputOption::VALUE_OPTIONAL,
                'Project directory glob patterns',
                $this->variables->get('projects'))
            ->addArgument('packages',
                InputArgument::OPTIONAL,
                'Package name regex pattern',
                $this->variables->get('packages'))
            ->addOption('by', null,
                InputOption::VALUE_REQUIRED,
                'Group By',
                'package');
    }

    protected function handle() {
        switch ($this->by) {
            case 'project': $this->usagesByProject(); break;
            case 'package': $this->usagesByPackage(); break;
            default: throw new \Exception("--by={$this->by} not supported");
        }
   }

    protected function usagesByProject() {
        foreach ($this->usages_by_project as $group => $usages) {
            /* @var object[]|UsageHint[] $usages */

            $this->output->writeln($group);
            foreach ($usages as $usage) {
                $this->output->writeln(sprintf("    %-40s %s <- %s",
                    $usage->package,
                    $usage->version, $usage->constraints));
            }
        }
    }

    protected function usagesByPackage() {
        foreach ($this->usages_by_package as $group => $usages) {
            /* @var object[]|UsageHint[] $usages */

            $this->output->writeln($group);
            foreach ($usages as $usage) {
                $this->output->writeln(sprintf("    %-40s %s <- %s",
                    mb_strlen($usage->project) > 40
                        ? mb_substr($usage->project, mb_strlen($usage->project) - 40)
                        : $usage->project,
                    $usage->version, $usage->constraints));
            }
        }
    }
}