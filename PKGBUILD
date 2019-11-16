# Maintainer: Chris Harvey <chris@chrisnharvey.com>

pkgname=snapman
_pkgname=snapman
pkgver=10.17de613
pkgrel=1
pkgdesc="Automatically create Btrfs snapshots before any Pacman transaction and automatically purge old snapshots."
arch=("x86_64")
url="https://github.com/chrisnharvey/snapman"
license=(GPL)
depends=(php btrfs-progs)
makedepends=(git composer)
optdepends=('grub-btrfs: automatically add boot entries for snapshots')
source=("git+https://github.com/chrisnharvey/snapman")
sha256sums=('SKIP')

pkgver() {
    cd "${srcdir}/${_pkgname}"
    echo $(git rev-list --count HEAD).$(git rev-parse --short HEAD)
}

build() {
    cd "${srcdir}/${_pkgname}"
    composer install
    php vendor/bin/encore build snapman.php
    chmod +x snapman.php.phar
}

package() {
    # mkdir "$pkgdir/etc"
	# cp -r conf/snapman.conf "$pkgdir/etc/snapman.conf"

    mkdir -p "$pkgdir/usr/bin"
	cp "${srcdir}/${_pkgname}/snapman.php.phar" "$pkgdir/usr/bin/snapman"
}

