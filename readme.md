
## Usage

Currently, this repository contains a single command:

    projects show:packages

Using this command, you can see:

* Laravel version each project is using;
* the versions of your own packages used in various projects.
    
Sample output:

    my/package1
        full/project1/path        v3.4.2 <- ^3.0,^3.2
    my/package2
        full/project1/path        v1.0.0 <- ^1
        full/project2/path        v1.x-dev <- ^1,v1.x-dev

The first version in the output is the actually installed version, the others are the versions required in the project's `composer.json` or in the `composer.json` of the other packages.

By default, it groups the output by a package. Add `--by=project` to group it by a project instead. 

## Installation

Prerequisites: 

* PHP 7.2 or later 
* Composer.

Install the package using the following command:

    composer global require osmscripts/projects

## Add The Script Directory To `PATH`

The package contains `projects` command-line script. To run the script from any directory, add the directory containing the script to the operating system `PATH` environment variable.

### Windows

1. Open Windows `Start` menu, type in `env` and pick `Edit the system environment variables`.

2. Click `Environment variables` button.

3. Under `User variables`, double-click `PATH` variable and add the path to Composer's `vendor/bin` directory.

    In my case, it is
    
        C:\Users\Vladislav\AppData\Roaming\Composer\vendor\bin

4. Press `OK`, `Apply`, `OK`.


### Linux

Add the following line to `~/.profile`:

    PATH="$HOME/.composer/vendor/bin:$PATH"

## Configuration

Specify where your projects are, and the names of the packages: 

    projects var projects={path_globs_separated_by_semicolon}
    projects var packages={package_name_regex}

Here is how I use it:

    projects var projects=C:\Users\Vladislav\AppData\Roaming\Composer;d:\_projects\*
    projects var "packages=osmphp/|osmscripts/|osmianski/"

You may also provide these variables directly in the command line.

Provide package name regex in a command line argument:

    projects show:packages osmphp/ 

Provide project paths in a command line option:

    projects show:packages --projects=d:\_projects\*

## License And Credits ##

Copyright (C) 2020 - Vladislav Ošmianskij.

All files of this package are licensed under [GPL-3.0](/LICENSE).

Created using [OsmScripts](https://github.com/osmscripts/osmscripts).
