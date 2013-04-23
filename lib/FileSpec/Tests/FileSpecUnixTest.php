<?php

require_once '../Unix.php';

use \stephenca\FileSpec\Unix;

class FileSpecUnixTest extends PHPUnit_Framework_TestCase
{
    public function test_canonpath ()
    {
        $this->assertEquals( '/a/b/../c', \stephenca\FileSpec\Unix\canonpath('///../../..//./././a//b/.././c/././'));
        $this->assertEquals('', \stephenca\FileSpec\Unix\canonpath(''));
        $this->assertEquals('a/../../b/c',\stephenca\FileSpec\Unix\canonpath('a/../../b/c'));
        $this->assertEquals('/',\stephenca\FileSpec\Unix\canonpath('/.'));
        $this->assertEquals('/',\stephenca\FileSpec\Unix\canonpath('/./'));
        $this->assertEquals('/a',\stephenca\FileSpec\Unix\canonpath('/a/./'));
        $this->assertEquals('/a',\stephenca\FileSpec\Unix\canonpath('/a/.'));
        $this->assertEquals('/',\stephenca\FileSpec\Unix\canonpath('/../../'));
        $this->assertEquals('/',\stephenca\FileSpec\Unix\canonpath('/../..'));
    }

    /*
     * canonpath
[ "Unix->canonpath('///../../..//./././a//b/.././c/././')",   '/a/b/../c' ],
[ "Unix->canonpath('')",                       ''               ],
# rt.perl.org 27052
[ "Unix->canonpath('a/../../b/c')",            'a/../../b/c'    ],
[ "Unix->canonpath('/.')",                     '/'              ],
[ "Unix->canonpath('/./')",                    '/'              ],
[ "Unix->canonpath('/a/./')",                  '/a'             ],
[ "Unix->canonpath('/a/.')",                   '/a'             ],
[ "Unix->canonpath('/../../')",                '/'              ],
[ "Unix->canonpath('/../..')",                 '/'              ],
     */

    public function test_catdir ()
    {
        $this->assertEquals( '', \stephenca\FileSpec\Unix\catdir() );
        $this->assertEquals('/', \stephenca\FileSpec\Unix\catdir(array('')) );
        $this->assertEquals('/', \stephenca\FileSpec\Unix\catdir(array('/')));
        $this->assertEquals('/d1/d2/d3', \stephenca\FileSpec\Unix\catdir(array('','d1','d2','d3','')));
        $this->assertEquals('d1/d2/d3',\stephenca\FileSpec\Unix\catdir(array('d1','d2','d3','')));
        $this->assertEquals('/d1/d2/d3',\stephenca\FileSpec\Unix\catdir(array('','d1','d2','d3')));
        $this->assertEquals('d1/d2/d3',\stephenca\FileSpec\Unix\catdir(array('d1','d2','d3')));
        $this->assertEquals('/d2/d3' ,\stephenca\FileSpec\Unix\catdir(array('/','d2/d3')));
    }
    /*
     * catdir()
[ "Unix->catdir()",                     ''          ],
[ "Unix->catdir('')",                   '/'         ],
[ "Unix->catdir('/')",                  '/'         ],
[ "Unix->catdir('','d1','d2','d3','')", '/d1/d2/d3' ],
[ "Unix->catdir('d1','d2','d3','')",    'd1/d2/d3'  ],
[ "Unix->catdir('','d1','d2','d3')",    '/d1/d2/d3' ],
[ "Unix->catdir('d1','d2','d3')",       'd1/d2/d3'  ],
[ "Unix->catdir('/','d2/d3')",          '/d2/d3'    ],
     */

    public function test_catfile ()
    {
        $this->assertEquals('a/b/c',\stephenca\FileSpec\Unix\catfile(array('a','b','c')));
        $this->assertEquals('a/b/c',\stephenca\FileSpec\Unix\catfile(array('a','b','./c')));
        $this->assertEquals('a/b/c',\stephenca\FileSpec\Unix\catfile(array('./a','b','c')));
        $this->assertEquals('c',\stephenca\FileSpec\Unix\catfile(array('c')));
        $this->assertEquals('c',\stephenca\FileSpec\Unix\catfile(array('./c')));
        $this->assertEquals('a/b/c',\stephenca\FileSpec\Unix\catfile(array( 'a/b','c')));
    }

    /*
[ "Unix->catfile('a','b','c')",         'a/b/c'  ],
[ "Unix->catfile('a','b','./c')",       'a/b/c'  ],
[ "Unix->catfile('./a','b','c')",       'a/b/c'  ],
[ "Unix->catfile('c')",                 'c' ],
[ "Unix->catfile('./c')",               'c' ],
     */

    public function test_splitpath ()
    {
        $match =\stephenca\FileSpec\Unix\splitpath('file');
        $this->assertEquals('',$match[0]);
        $this->assertEquals('',$match[1]);
        $this->assertEquals('file',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath('/d1/d2/d3/');
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/d1/d2/d3/',$match[1]);
        $this->assertEquals('',$match[2]);

        $match=\stephenca\FileSpec\Unix\splitpath( 'd1/d2/d3/' );
        $this->assertEquals('',$match[0]);
        $this->assertEquals('d1/d2/d3/',$match[1]);
        $this->assertEquals('',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( '/d1/d2/d3/.');
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/d1/d2/d3/.',$match[1]);
        $this->assertEquals('',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( '/d1/d2/d3/..' );
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/d1/d2/d3/..',$match[1]);
        $this->assertEquals('',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( '/d1/d2/d3/.file' );
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/d1/d2/d3/',$match[1]);
        $this->assertEquals('.file',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( 'd1/d2/d3/file' );
        $this->assertEquals('',$match[0]);
        $this->assertEquals('d1/d2/d3/',$match[1]);
        $this->assertEquals('file',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( '/../../d1/' );
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/../../d1/',$match[1]);
        $this->assertEquals('',$match[2]);

        $match =\stephenca\FileSpec\Unix\splitpath( '/././d1/');
        $this->assertEquals('',$match[0]);
        $this->assertEquals('/././d1/',$match[1]);
        $this->assertEquals('',$match[2]);

        /*
[ "Unix->splitpath('file')",            ',,file'            ],
[ "Unix->splitpath('/d1/d2/d3/')",      ',/d1/d2/d3/,'      ],
[ "Unix->splitpath('d1/d2/d3/')",       ',d1/d2/d3/,'       ],
[ "Unix->splitpath('/d1/d2/d3/.')",     ',/d1/d2/d3/.,'     ],
[ "Unix->splitpath('/d1/d2/d3/..')",    ',/d1/d2/d3/..,'    ],
[ "Unix->splitpath('/d1/d2/d3/.file')", ',/d1/d2/d3/,.file' ],
[ "Unix->splitpath('d1/d2/d3/file')",   ',d1/d2/d3/,file'   ],
[ "Unix->splitpath('/../../d1/')",      ',/../../d1/,'      ],
[ "Unix->splitpath('/././d1/')",        ',/././d1/,'        ],
    */
        /*
         [ "Unix->splitdir('')",           ''           ],
[ "Unix->splitdir('/d1/d2/d3/')", ',d1,d2,d3,' ],
[ "Unix->splitdir('d1/d2/d3/')",  'd1,d2,d3,'  ],
[ "Unix->splitdir('/d1/d2/d3')",  ',d1,d2,d3'  ],
[ "Unix->splitdir('d1/d2/d3')",   'd1,d2,d3'   ],
         */

    }
}
