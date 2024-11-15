You can contribute with icons. Best if you install the theme on your GNU/Linux distribution and contribute with missing icons.
If you don't have Linux, you can install one on Virtual Machine check this search query:
"[how to install linux on virtualbox](https://duckduckgo.com/?q=how+to+install+linux+on+virtualbox&ia=web)".


## How to contribute icons

Icons are create using SVG file format, it's XML similar to HTML with with different tags.
To create new icon I suggest using [Inkscape](https://inkscape.org/) vector graphic program.
If you want to create icon for application you can take existing icon from different theme,
and modify it to be single shape. The easiest way to create proper icon is to:

* Remove all groups from the shape
* Make sure that the icon have the size of 128x128 px
* Merge all the shapes into single shape
* Use XML tool and find path XML tag
* Copy d attribbute content of path tag
* Use script `mkshape > /PATH/TO/ICON.svg`

The script works only on Linux system.

If you use different flow to create icons, make sure that icon is 128x128px in size.
You can also open src/shape_template.svg file in Inkscape and create your icon there, but the icon
need to have single path XML tag with d attribute, the color doesn't matter but it would be nice
if you add style `style="fill:#aaaaaa"` so all source icons look the same. You can add your own
meta tags in the file but the license need to be http://creativecommons.org/licenses/by-sa/3.0/
