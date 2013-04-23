<?php

/**
 *
 * FileSpecUnix
 *
 * PHP 5
 *
 * (c) Stephen Cardie <stephenca@ls26.net>
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2013, Stephen Cardie <stephenca@ls26.net>
 * @package       FileSpecUnix
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

namespace stephenca\FileSpec\Unix;

/**
 * FileSpecUnix is a port of the Unix elements of the Perl module File::Spec (http://search.cpan.org/~smueller/PathTools-3.33/) to php.
 *
 * The following function documentation is largely taken that of the Perl
 * module.
 *
 */

/**
 *
 * No physical check on the filesystem, but a logical cleanup of a path.
 *
 * Usage:
 *
 * $cpath = canonpath( $path ) ;
 *
 * Note that this does *not* collapse x/../y sections into y. This is by design. If /foo on your system is a symlink to /bar/baz, then /foo/../quux is actually /bar/quux, not /quux as a naive ../-removal would give you. If you want to do this kind of processing, you probably want Cwd's realpath() function to actually traverse the filesystem cleaning up paths like this.
 *
 * @param string $path a string containing a path to clean.
 * @return string cleaned path.
 *
 */

function canonpath ($path)
{
    if (!isset($path)) {
        return false;
    } else {
      $path =  preg_replace('~/{2,}~','/',$path);
      //$path =~ s{/{2,}}{/}g;                        # xx////xx  -> xx/xx
      $path = preg_replace('~(?:/\.)+(?:/|\z)~','/',$path);
      //$path =~ s{(?:/\.)+(?:/|\z)}{/}g;             # xx/././xx -> xx/xx
      if ($path == "./") {
      } else {
          $path = preg_replace('~^(?:\./)+~','',$path);
          //$path =~ s{^(?:\./)+}{}s ; # ./xx      -> xx
      }
      $path = preg_replace('~^/(?:\.\./)+~','/',$path);
      //$path =~ s{^/(?:\.\./)+}{/};                   # /../../xx -> xx
      $path = preg_replace('~^/\.\.$~','/',$path);
      //$path =~ s{^/\.\.$}{/};                          # /..       -> /
      if ($path == "/") {
      } else {
          $path = preg_replace('~/\z~','',$path);
          //$path =~ s{/\z}{};          # xx/       -> xx
      }

      return $path;
  }
}

/**
 * Concatenate two or more directory names to form a complete path ending with a directory. But remove the trailing slash from the resulting string, because it doesn't look good, isn't necessary and confuses OS/2. Of course, if this is the root directory, don't cut off the trailing slash :-)
 *
 * Usage:
 *
 * $path = catdir( array( $dir_1, $dir_2 ) );
 *
 * @param array $paths list of directories to concatenate.
 * @return string complete path.
 *
 */

function catdir( array $paths  = array() )
{
  array_push($paths, '');

  return canonpath(implode('/',$paths));
}

/**
 *
 * Concatenate one or more directory names and a filename to form a complete path ending with a filename
 *
 * Usage:
 *
 * $path = catfile( array( $dir_1, $dir_2, $filename ) );
 *
 * @param array $paths list of directories and a filename.
 * @return string cleaned-up path with filename appended.
 *
 */
function catfile ( array $paths = array() )
{
    $file = canonpath(array_pop($paths));
    if (count($paths)==0) {
        return $file;
    } else {
        $dir = catdir($paths);
        if (substr($dir,-1) == '/') {
            return $dir . $file;
        } else {
            return $dir . '/' . $file;
        }
    }
}

/**
 * Returns a string representation of the current directory.
 *
 * Usage:
 *
 * $curdir = curdir();
 *
 * @param none
 * @return string.
 *
 */

function curdir ()
{
    return '.';
}

/**
 * Returns a string representation of the null device.
 *
 * Usage:
 *
 * $devnull = File::Spec->devnull();
 *
 * @param none
 * @return string
 *
 */

function devnull ()
{
    return '/dev/null';
}

/**
 * Returns a string representation of the root directory.
 *
 * Usage:
 *
 * $rootdir = File::Spec->rootdir();
 *
 * @param none.
 * @return string
 *
 */

function rootdir ()
{
    return '/';
}

function splitpath ($path,$nofile = 0)
{
    $volume = '';
    $directory = '';
    $file = '';

    if ($nofile) {
        $directory = $path;
    } else {
        preg_match(
            '~^((?:.*/(?:\.\.?\z)?)?)([^/]*)~',
            $path,
            $matches);
        $directory = isset($matches[1]) ? $matches[1] : '';
        $file = isset($matches[2]) ? $matches[2] : '';
        //$path =~ m|^ ( (?: .* / (?: \.\.?\z )? )? ) ([^/]*) |xs;
    }

    return( array( $volume, $directory, $file ) );
}
