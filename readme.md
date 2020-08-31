
Currently, this repository contains a single command:

    projects show:packages
    
It shows which Composer package versions are used in your projects. With this command you can see Laravel version each project is using or if you develop your own packages, the versions of these packages in various projects.

Created using [OsmScripts](https://github.com/osmscripts/osmscripts).

Requires PHP 7.2 or later and Composer.

## Installation

Install the package using the following command:

    composer global require osmscripts/projects

## Add The Script Directory To Path

The package contains `projects` command-line script. To run the script from any directory, add the directory containing the script to the operating system `PATH` environment variable.

On Windows:

1. Open Windows `Start` menu, type in `env` and pick `Edit the system environment variables`.

2. Click `Environment variables` button.

3. Under `User variables`, double-click `PATH` variable and add the path to Composer's `vendor/bin` directory.

    In my case, it is
    
        C:\Users\Vladislav\AppData\Roaming\Composer\vendor\bin

4. Press `OK`, `Apply`, `OK`.


On Linux, add the following line to `~/.profile`:

    PATH="$HOME/.composer/vendor/bin:$PATH"


## Configuration

Specify where your projects are, and the names of the packages: 

    projects var projects={path_globs_separated_by_semicolon}
    projects var packages={package_name_regex}

Here is how I use it:

    projects var projects=C:\Users\Vladislav\AppData\Roaming\Composer;d:\_projects\*
    projects var "packages=osmphp\/|osmscripts\/|osmianski\/"

## License And Credits ##

Copyright (C) 2020 - Vladislav Ošmianskij.

All files of this package are licensed under [GPL-3.0](/LICENSE).
