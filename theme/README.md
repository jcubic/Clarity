# Clarity Vector Icon theme for GTK

![Clarity Icon Theme](.github/preview.png)

This theme is based on AwOken and Token, lots of shapes and basic color pallete was
taken from these icons. Few icons was taken from Raphael <http://raphaeljs.com/>.
I use some shapes from OpenClipart <http://www.openclipart.org>, Wikipedia, Humanity,
AnyColorYouLike Themes and Emoji One icons. The rest of icons I design myself
by simplifying existed icons or logos. I use mostly two fonts: Impact and Cheboygan.

Icons are mostly monoshape, but some icons have additional elements. There are nine
predefined color themes (in five colors) but you can also create your own.

Available themes:

| Name           | Description             |
|----------------|-------------------------|
| violaceus      | violet/pink gradient    |
| lux_violaceus  | glossy variant          |
| canus          | grey gradient (default) |
| dark_canus     | dark grey gradient      |
| caeruleus      | blue gradient           |
| lux_caeruleus  | glossy variant          |
| viridis        | green gradient          |
| luteus         | orange gradient         |
| albus          | white solid             |

## Installation

### Quick Install (recommended)

```bash
curl -sL https://clarity.pl.eu.org/install | bash
```

This installs the `clarity` CLI manager and base icon sources to `~/.clarity-icons/`.

### Install from Source

```bash
git clone https://github.com/jcubic/Clarity.git
cd Clarity/theme
make install
```

This copies the CLI to `~/.local/bin/clarity`, shell completions, and icon sources
to `~/.clarity-icons/`. No root required.

To uninstall:

```bash
make uninstall
```

### Install from Packages

Download `.deb` or `.rpm` packages from the
[GitHub releases](https://github.com/jcubic/Clarity/releases) page.

```bash
# Debian/Ubuntu
sudo dpkg -i clarity-icons_*.deb

# Fedora/RHEL
sudo rpm -i clarity-icons-*.rpm
```

## Usage

```bash
clarity use caeruleus           # switch to the blue variant
clarity use dark_canus          # switch to the dark grey variant
clarity list                    # show available variants
clarity --help                  # show all commands
```

### Community Themes

```bash
clarity install @bob/cyberpunk  # install a community theme
clarity use @bob/cyberpunk      # activate it
clarity remove @bob/cyberpunk   # remove it
clarity update                  # update CLI, base theme, and community themes
```

### Create Your Own Theme

Create a custom template SVG with a `<circle id="icon-placeholder">` element,
then use the CLI to build icons from it:

```bash
clarity create ./my-template.svg
clarity use @local/my-template
```

Upload your theme to the [gallery](https://clarity.pl.eu.org/#gallery) to share
it with the community.

## Building Packages

```bash
make deb      # build .deb package (requires dpkg-deb)
make rpm      # build .rpm package (requires rpmbuild)
make tar.gz   # build distributable tarball
```

Or use Docker to build all packages:

```bash
docker build -t clarity-packages .
docker run --rm -v "$(pwd)/dist:/out" clarity-packages
```

## Other Contributors

* Enneric Valmorin
* Chris Lenk

## License

Copyright (c) 2010-2026 [Jakub T. Jankiewicz](https://jcubic.pl/me)
Licensed under [CC-BY-SA 4.0 license](http://creativecommons.org/licenses/by-sa/4.0/)
horned skull by [Juicy Fish](https://thenounproject.com/creator/admin885/) from [Noun Project](https://thenounproject.com/browse/icons/term/horned-skull/) (CC BY 3.0)
