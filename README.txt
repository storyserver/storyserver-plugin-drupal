CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Maintainers


INTRODUCTION
------------

StoryServer is an online image and metadata publishing service designed to
serve relatively small collections of curated images, or stories. StoryServer
is ideally suited for publishing editorials, galleries, portfolios, and other
short visual narratives.

Drupal 7 and 8 modules provide field API integration via a custom StoryServer
field and field formatters. The modules connect to the StoryServer API via a
configured key pair. The contrib modules are stable, however, StoryServer
itself is in an early beta stage of development, which means that we're not
ready to open our doors just yet. Feel free to request an invitation and we'll
send an invite and registration details for an early release of StoryServer
when it's ready.

  * For a full description of the module, visit the project page:
    https://www.drupal.org/sandbox/blue_waters/2669382

  * To submit bug reports and feature suggestions, or to track changes:
    https://www.drupal.org/project/issues/2669382


REQUIREMENTS
------------

This module requires the StoryServer PHP client library.

  * See the composer installation instructions below.

INSTALLATION
------------

  * Install as you would normally install a contributed Drupal module. See:
    https://www.drupal.org/documentation/install/modules-themes/modules-7 for
    Drupal 7 modules and
    https://www.drupal.org/documentation/install/modules-themes/modules-8
    for Drupal 8 modules.

  * NOTE: This module has Composer dependencies which must be installed with
    https://getcomposer.org/. Once composer is installed, change into the
    directory containing this module and run 'composer install'.


CONFIGURATION
------------
  * Request a key pair from https://storyserver.io, and enter the key id and
    secret key in the configuration settings form.

  * Edit an existing or new content type, and add the StoryServer custom field
    to the content type definition under 'manage fields'.

  * When creating a new node, you can chose from a published story on
    https://storyserver.io, as well as a display theme.


MAINTAINERS
-----------

Current maintainers:
  * Anthony Bouch (blue_waters) - https://www.drupal.org/u/blue_waters