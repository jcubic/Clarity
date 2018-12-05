# This is generated Makefile from Clarity Vector Icon Theme for GTK
#
# Copyright (c) 2010-2018 Jakub Jankiewicz <http://jcubic.pl/me>
# Licensed under CC-BY-SA 3.0 license
#
# This file was automaticly generated by configure script
# Don't modified it, if you want to do some changes, do this
# in configure script
#
SHELL := /bin/bash
DIR=`pwd | sed 's%.*/%%'`
SYMLINKS=grep -v '^\#' src/symlinks | tr -d '\r' | grep -v '^ *$$'

ALL: canus

scalable: src/symlinks
	@echo creating symlinks
	@test -d scalable || mkdir scalable
	@test -d scalable/actions || mkdir scalable/actions
	@test -d scalable/apps || mkdir scalable/apps
	@test -d scalable/categories || mkdir scalable/categories
	@test -d scalable/devices || mkdir scalable/devices
	@test -d scalable/distributor-logos || mkdir scalable/distributor-logos
	@test -d scalable/emblems || mkdir scalable/emblems
	@test -d scalable/mimetypes || mkdir scalable/mimetypes
	@test -d scalable/places || mkdir scalable/places
	@test -d scalable/status || mkdir scalable/status
	@test -d scalable/stock || mkdir scalable/stock
	@sed 's/^/ln -sf /g' <($(SYMLINKS)) | bash

static-files:
	@bash -c "find static -type f | sed -e 's/[^\/]*\/\(.*\)/echo copy scalable\/\1;cp & scalable\/\1/' | bash"

albus: scalable static-files gen_albus elements _16x16

gen_albus:
	@echo 'Building icons for theme albus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build albus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

caeruleus: scalable static-files gen_caeruleus elements _16x16

gen_caeruleus:
	@echo 'Building icons for theme caeruleus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build caeruleus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

canus: scalable static-files gen_canus elements _16x16

gen_canus:
	@echo 'Building icons for theme canus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build canus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

dark_canus: scalable static-files gen_dark_canus elements _16x16

gen_dark_canus:
	@echo 'Building icons for theme dark_canus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build dark_canus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

luteus: scalable static-files gen_luteus elements _16x16

gen_luteus:
	@echo 'Building icons for theme luteus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build luteus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

lux_caeruleus: scalable static-files gen_lux_caeruleus elements _16x16

gen_lux_caeruleus:
	@echo 'Building icons for theme lux_caeruleus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build lux_caeruleus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

lux_violaceus: scalable static-files gen_lux_violaceus elements _16x16

gen_lux_violaceus:
	@echo 'Building icons for theme lux_violaceus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build lux_violaceus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

violaceus: scalable static-files gen_violaceus elements _16x16

gen_violaceus:
	@echo 'Building icons for theme violaceus ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build violaceus $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

viridis: scalable static-files gen_viridis elements _16x16

gen_viridis:
	@echo 'Building icons for theme viridis ... '
	@bash -c 'for i in `find src -mindepth 2 -name "*.svg" -type f`; do \
./build viridis $$i;\
echo building `echo $$i | sed -e "s/src/scalable/"`;\
done;'

elements:
	@echo -n 'building additional icons ... '
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus.svg`\n</svg>%" scalable/devices/drive-harddisk.svg > scalable/devices/hdd_unmount.svg;
	@echo "building scalable/devices/hdd_unmount.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus.svg`\n</svg>%" scalable/devices/camera-photo.svg > scalable/devices/camera_unmount.svg;
	@echo "building scalable/devices/camera_unmount.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus.svg`\n</svg>%" scalable/actions/document-save.svg > scalable/devices/3floppy_unmount.svg;
	@echo "building scalable/devices/3floppy_unmount.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus.svg`\n</svg>%" scalable/devices/media-optical-cd.svg > scalable/devices/cdrom_unmount.svg;
	@echo "building scalable/devices/cdrom_unmount.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus.svg`\n</svg>%" scalable/devices/drive-removable-media-usb.svg > scalable/devices/usbpendrive_unmount.svg;
	@echo "building scalable/devices/usbpendrive_unmount.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus_bottom.svg`\n</svg>%" scalable/status/stock_weather-cloudy.svg > scalable/status/ubuntuone-client-offline.svg;
	@echo "building scalable/status/ubuntuone-client-offline.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_minus_bottom.svg`\n</svg>%" scalable/actions/bookmark-new.svg > scalable/apps/stock_delete-bookmark.svg;
	@echo "building scalable/apps/stock_delete-bookmark.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/network-idle.svg > scalable/status/network-offline.svg;
	@echo "building scalable/status/network-offline.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/devices/input-touchpad.svg > scalable/actions/touchpad-disabled.svg;
	@echo "building scalable/actions/touchpad-disabled.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/devices/printer.svg > scalable/status/printer-error.svg;
	@echo "building scalable/status/printer-error.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/stock/epiphany-history.svg > scalable/status/appointment-missed.svg;
	@echo "building scalable/status/appointment-missed.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/actions/bookmark-new.svg > scalable/actions/bookmark-missing.svg;
	@echo "building scalable/actions/bookmark-missing.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/actions/mail_spam.svg > scalable/actions/stock_not-spam.svg;
	@echo "building scalable/actions/stock_not-spam.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/avatar-default.svg > scalable/status/user-offline.svg;
	@echo "building scalable/status/user-offline.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel.svg`\n</svg>%" scalable/emblems/emblem-personal.svg > scalable/stock/stock_lock-broken.svg;
	@echo "building scalable/stock/stock_lock-broken.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/xfpm-battery-000.svg > scalable/status/xfpm-battery-missing.svg;
	@echo "building scalable/status/xfpm-battery-missing.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/xfpm-ups-000.svg > scalable/status/xfpm-ups-missing.svg;
	@echo "building scalable/status/xfpm-ups-missing.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/xfpm-brightness-kdb.svg > scalable/status/xfpm-brightness-kbd-disabled.svg;
	@echo "building scalable/status/xfpm-brightness-kbd-disabled.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/apps/gnome-brightness-applet.svg > scalable/status/xfpm-brightness-lcd-disabled.svg;
	@echo "building scalable/status/xfpm-brightness-lcd-disabled.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/dropboxstatus-logo.svg > scalable/status/dropboxstatus-x.svg;
	@echo "building scalable/status/dropboxstatus-x.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/status/nm-signal-00.svg > scalable/status/notification-network-wireless-disconnected.svg;
	@echo "building scalable/status/notification-network-wireless-disconnected.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/devices/network-wired.svg > scalable/status/notification-network-ethernet-disconnected.svg;
	@echo "building scalable/status/notification-network-ethernet-disconnected.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/devices/microphone.svg > scalable/status/microphone-sensitivity-muted.svg;
	@echo "building scalable/status/microphone-sensitivity-muted.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/apps/email.svg > scalable/actions/mail_new.svg;
	@echo "building scalable/actions/mail_new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/apps/gnote.svg > scalable/actions/note-new.svg;
	@echo "building scalable/actions/note-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/places/notebook.svg > scalable/actions/notebook-new.svg;
	@echo "building scalable/actions/notebook-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/apps/ooo.svg > scalable/apps/openoffice-new.svg;
	@echo "building scalable/apps/openoffice-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/places/folder.svg > scalable/actions/folder-new.svg;
	@echo "building scalable/actions/folder-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/actions/contact.svg > scalable/actions/contact-new.svg;
	@echo "building scalable/actions/contact-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/mimetypes/gtk-file.svg > scalable/actions/document-new.svg;
	@echo "building scalable/actions/document-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/status/avatar-default.svg > scalable/actions/stock_new-bcard.svg;
	@echo "building scalable/actions/stock_new-bcard.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/apps/evolution-addressbook.svg > scalable/actions/address-book-new.svg;
	@echo "building scalable/actions/address-book-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/apps/preferences-mail-accounts.svg > scalable/stock/stock_new-meeting.svg;
	@echo "building scalable/stock/stock_new-meeting.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/stock/epiphany-history.svg > scalable/actions/appointment-new.svg;
	@echo "building scalable/actions/appointment-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/devices/media-optical-audio.svg > scalable/actions/audio-cd-new.svg;
	@echo "building scalable/actions/audio-cd-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/actions/bookmark-new.svg > scalable/actions/bookmark_add.svg;
	@echo "building scalable/actions/bookmark_add.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/apps/gnote.svg > scalable/apps/stock_insert-note.svg;
	@echo "building scalable/apps/stock_insert-note.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/mimetypes/audio-x-mp3-playlist.svg > scalable/actions/playlist-new.svg;
	@echo "building scalable/actions/playlist-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/actions/add-files-to-archive.svg;
	@echo "building scalable/actions/add-files-to-archive.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/devices/drive-optical.svg > scalable/stock/stock_xfburn-new-data-composition.svg;
	@echo "building scalable/stock/stock_xfburn-new-data-composition.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/devices/drive-harddisk.svg > scalable/apps/ubiquity.svg;
	@echo "building scalable/apps/ubiquity.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus_bottom.svg`\n</svg>%" scalable/apps/easytags.svg > scalable/actions/tag-new.svg;
	@echo "building scalable/actions/tag-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_plus.svg`\n</svg>%" scalable/devices/printer.svg > scalable/devices/gnome-dev-printer-new.svg;
	@echo "building scalable/devices/gnome-dev-printer-new.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_warning_bottom.svg`\n</svg>%" scalable/status/network-idle.svg > scalable/status/network-error.svg;
	@echo "building scalable/status/network-error.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_warning_bottom.svg`\n</svg>%" scalable/status/stock_weather-cloudy.svg > scalable/status/weather-severe-alert.svg;
	@echo "building scalable/status/weather-severe-alert.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_warning_bottom.svg`\n</svg>%" scalable/devices/drive-harddisk.svg > scalable/apps/gdu-warning.svg;
	@echo "building scalable/apps/gdu-warning.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_warning_bottom.svg`\n</svg>%" scalable/devices/printer.svg > scalable/actions/gtk-print-warning.svg;
	@echo "building scalable/actions/gtk-print-warning.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_warning_bottom.svg`\n</svg>%" scalable/devices/drive-harddisk.svg > scalable/apps/xfce4-fsguard-plugin-warning.svg;
	@echo "building scalable/apps/xfce4-fsguard-plugin-warning.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_warning_bottom.svg`\n</svg>%" scalable/status/system-devices-panel.svg > scalable/status/system-devices-panel-alert.svg;
	@echo "building scalable/status/system-devices-panel-alert.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_bottom.svg`\n</svg>%" scalable/devices/printer.svg > scalable/status/printer-printing.svg;
	@echo "building scalable/status/printer-printing.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_bottom.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/apps/gdeb.svg;
	@echo "building scalable/apps/gdeb.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_bottom.svg`\n</svg>%" scalable/devices/drive-optical.svg > scalable/apps/ogmrip.svg;
	@echo "building scalable/apps/ogmrip.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_bottom.svg`\n</svg>%" scalable/actions/mail-copy.svg > scalable/actions/mail-move.svg;
	@echo "building scalable/actions/mail-move.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_bottom.svg`\n</svg>%" scalable/emblems/emblem-web.svg > scalable/actions/torrent-search-download.svg;
	@echo "building scalable/actions/torrent-search-download.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_up_arrow.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/actions/extract-archive.svg;
	@echo "building scalable/actions/extract-archive.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_up_arrow.svg`\n</svg>%" scalable/places/user-trash.svg > scalable/actions/gtk-undelete.svg;
	@echo "building scalable/actions/gtk-undelete.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-100.svg > scalable/status/xfpm-battery-100-charging.svg;
	@echo "building scalable/status/xfpm-battery-100-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-080.svg > scalable/status/xfpm-battery-080-charging.svg;
	@echo "building scalable/status/xfpm-battery-080-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-060.svg > scalable/status/xfpm-battery-060-charging.svg;
	@echo "building scalable/status/xfpm-battery-060-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-040.svg > scalable/status/xfpm-battery-040-charging.svg;
	@echo "building scalable/status/xfpm-battery-040-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-020.svg > scalable/status/xfpm-battery-020-charging.svg;
	@echo "building scalable/status/xfpm-battery-020-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_bolt.svg`\n</svg>%" scalable/status/xfpm-battery-000.svg > scalable/status/xfpm-battery-000-charging.svg;
	@echo "building scalable/status/xfpm-battery-000-charging.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_clock_bottom.svg`\n</svg>%" scalable/apps/email.svg > scalable/actions/mail-notification.svg;
	@echo "building scalable/actions/mail-notification.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_clock_bottom.svg`\n</svg>%" scalable/stock/epiphany-history.svg > scalable/actions/stock_timezone.svg;
	@echo "building scalable/actions/stock_timezone.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_blue_info_bottom.svg`\n</svg>%" scalable/devices/printer.svg > scalable/actions/gtk-print-report.svg;
	@echo "building scalable/actions/gtk-print-report.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_blue_info_bottom.svg`\n</svg>%" scalable/status/avatar-default.svg > scalable/apps/user-info.svg;
	@echo "building scalable/apps/user-info.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_blue_info_bottom.svg`\n</svg>%" scalable/devices/generic-card.svg > scalable/apps/hwbrowser.svg;
	@echo "building scalable/apps/hwbrowser.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_blue_info_bottom.svg`\n</svg>%" scalable/status/system-devices-panel.svg > scalable/status/system-devices-panel-information.svg;
	@echo "building scalable/status/system-devices-panel-information.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_bottom.svg`\n</svg>%" scalable/distributor-logos/debian.svg > scalable/categories/debian-security.svg;
	@echo "building scalable/categories/debian-security.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_bottom.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/actions/jockey-proprietary.svg;
	@echo "building scalable/actions/jockey-proprietary.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_bottom.svg`\n</svg>%" scalable/devices/generic-card.svg > scalable/apps/jockey.svg;
	@echo "building scalable/apps/jockey.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/gnome-mime-application-pdf.svg > scalable/mimetypes/application-x-gzpdf.svg;
	@echo "building scalable/mimetypes/application-x-gzpdf.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/application-postscript.svg > scalable/mimetypes/application-x-gzpostscript.svg;
	@echo "building scalable/mimetypes/application-x-gzpostscript.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/openofficeorg23-drawing.svg > scalable/mimetypes/image-x-gzeps.svg;
	@echo "building scalable/mimetypes/image-x-gzeps.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/image-x-svg+xml.svg > scalable/mimetypes/image-svg+xml-compressed.svg;
	@echo "building scalable/mimetypes/image-svg+xml-compressed.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/application-vnd.scribus.svg > scalable/mimetypes/packages-scribus.svg;
	@echo "building scalable/mimetypes/packages-scribus.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/application-x-slx.svg > scalable/mimetypes/packages-aqsis.svg;
	@echo "building scalable/mimetypes/packages-aqsis.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/font_truetype.svg > scalable/mimetypes/application-x-gz-font-linux-psf.svg;
	@echo "building scalable/mimetypes/application-x-gz-font-linux-psf.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/text-x-bibtex.svg > scalable/mimetypes/application-x-gzdvi.svg;
	@echo "building scalable/mimetypes/application-x-gzdvi.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_package_bottom.svg`\n</svg>%" scalable/mimetypes/text-x-sl.svg > scalable/mimetypes/model-x-rib-gzip.svg;
	@echo "building scalable/mimetypes/model-x-rib-gzip.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check_bottom.svg`\n</svg>%" scalable/status/dropboxstatus-logo.svg > scalable/status/dropboxstatus-idle.svg;
	@echo "building scalable/status/dropboxstatus-idle.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_reload_bottom.svg`\n</svg>%" scalable/status/dropboxstatus-logo.svg > scalable/status/dropboxstatus-busy.svg;
	@echo "building scalable/status/dropboxstatus-busy.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_reload_rotated_bottom.svg`\n</svg>%" scalable/status/dropboxstatus-logo.svg > scalable/status/dropboxstatus-busy2.svg;
	@echo "building scalable/status/dropboxstatus-busy2.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/complement_nm_signal_75.svg`\n</svg>%" scalable/status/part-nm-signal-75.svg > scalable/status/nm-signal-75.svg;
	@echo "building scalable/status/nm-signal-75.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/complement_nm_signal_50.svg`\n</svg>%" scalable/status/part-nm-signal-50.svg > scalable/status/nm-signal-50.svg;
	@echo "building scalable/status/nm-signal-50.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/complement_nm_signal_25.svg`\n</svg>%" scalable/status/part-nm-signal-25.svg > scalable/status/nm-signal-25.svg;
	@echo "building scalable/status/nm-signal-25.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting12.svg > scalable/status/nm-vpn-connecting12.svg;
	@echo "building scalable/status/nm-vpn-connecting12.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting11.svg > scalable/status/nm-vpn-connecting11.svg;
	@echo "building scalable/status/nm-vpn-connecting11.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting10.svg > scalable/status/nm-vpn-connecting10.svg;
	@echo "building scalable/status/nm-vpn-connecting10.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting09.svg > scalable/status/nm-vpn-connecting09.svg;
	@echo "building scalable/status/nm-vpn-connecting09.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting08.svg > scalable/status/nm-vpn-connecting08.svg;
	@echo "building scalable/status/nm-vpn-connecting08.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting07.svg > scalable/status/nm-vpn-connecting07.svg;
	@echo "building scalable/status/nm-vpn-connecting07.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting06.svg > scalable/status/nm-vpn-connecting06.svg;
	@echo "building scalable/status/nm-vpn-connecting06.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting05.svg > scalable/status/nm-vpn-connecting05.svg;
	@echo "building scalable/status/nm-vpn-connecting05.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting04.svg > scalable/status/nm-vpn-connecting04.svg;
	@echo "building scalable/status/nm-vpn-connecting04.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting03.svg > scalable/status/nm-vpn-connecting03.svg;
	@echo "building scalable/status/nm-vpn-connecting03.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting02.svg > scalable/status/nm-vpn-connecting02.svg;
	@echo "building scalable/status/nm-vpn-connecting02.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_lock_center.svg`\n</svg>%" scalable/status/nm-stage01-connecting01.svg > scalable/status/nm-vpn-connecting01.svg;
	@echo "building scalable/status/nm-vpn-connecting01.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check_bottom.svg`\n</svg>%" scalable/apps/bluetooth.svg > scalable/status/blueberry-tray-active.svg;
	@echo "building scalable/status/blueberry-tray-active.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_cancel_bottom.svg`\n</svg>%" scalable/apps/bluetooth.svg > scalable/status/blueberry-tray-disabled.svg;
	@echo "building scalable/status/blueberry-tray-disabled.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_reload.svg`\n</svg>%" scalable/status/stock_weather-cloudy.svg > scalable/status/ubuntuone-client-updating.svg;
	@echo "building scalable/status/ubuntuone-client-updating.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check.svg`\n</svg>%" scalable/devices/input-touchpad.svg > scalable/actions/touchpad-enabled.svg;
	@echo "building scalable/actions/touchpad-enabled.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check.svg`\n</svg>%" scalable/status/avatar-default.svg > scalable/status/user-available.svg;
	@echo "building scalable/status/user-available.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_bell_bottom.svg`\n</svg>%" scalable/stock/epiphany-history.svg > scalable/status/appointment-soon.svg;
	@echo "building scalable/status/appointment-soon.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_debian_logo.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/mimetypes/deb.svg;
	@echo "building scalable/mimetypes/deb.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_yellow_warning_bottom.svg`\n</svg>%" scalable/apps/email.svg > scalable/actions/mail-mark-important.svg;
	@echo "building scalable/actions/mail-mark-important.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check.svg`\n</svg>%" scalable/emblems/emblem-personal.svg > scalable/stock/stock_lock-ok.svg;
	@echo "building scalable/stock/stock_lock-ok.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/actions/jockey-free.svg;
	@echo "building scalable/actions/jockey-free.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_blue_earth_bottom.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/apps/edit-urpm-sources.svg;
	@echo "building scalable/apps/edit-urpm-sources.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_check_fit_monitor.svg`\n</svg>%" scalable/devices/monitor.svg > scalable/apps/checkbox.svg;
	@echo "building scalable/apps/checkbox.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_black_tux.svg`\n</svg>%" scalable/mimetypes/gtk-file.svg > scalable/mimetypes/vmlinuz.svg;
	@echo "building scalable/mimetypes/vmlinuz.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_red_rpm_bottom.svg`\n</svg>%" scalable/mimetypes/package-x-generic.svg > scalable/mimetypes/application-x-rpm.svg;
	@echo "building scalable/mimetypes/application-x-rpm.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/element_green_down_arrow_top.svg`\n</svg>%" scalable/apps/fedora-drive.svg > scalable/apps/anaconda.svg;
	@echo "building scalable/apps/anaconda.svg"
	@sed -e "s%</svg>%`grep 'id=\"shape\"' src/vlc_xmas_hat.svg`\n</svg>%" scalable/apps/vlc.svg > scalable/apps/vlc-xmas.svg;
	@echo "building scalable/apps/vlc-xmas.svg"
arch:
	ln -sf ../distributor-logos/arch.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/arch.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/arch.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/arch.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/arch.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/arch.png 16x16/places/distributor-logo.png

debian:
	ln -sf ../distributor-logos/debian.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/debian.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/debian.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/debian.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/debian.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/debian.png 16x16/places/distributor-logo.png

fedora:
	ln -sf ../distributor-logos/fedora.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/fedora.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/fedora.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/fedora.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/fedora.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/fedora.png 16x16/places/distributor-logo.png

gentoo:
	ln -sf ../distributor-logos/gentoo.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/gentoo.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/gentoo.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/gentoo.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/gentoo.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/gentoo.png 16x16/places/distributor-logo.png

gnome:
	ln -sf ../distributor-logos/gnome.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/gnome.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/gnome.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/gnome.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/gnome.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/gnome.png 16x16/places/distributor-logo.png

kubuntu:
	ln -sf ../distributor-logos/kubuntu.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/kubuntu.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/kubuntu.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/kubuntu.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/kubuntu.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/kubuntu.png 16x16/places/distributor-logo.png

madriva:
	ln -sf ../distributor-logos/madriva.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/madriva.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/madriva.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/madriva.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/madriva.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/madriva.png 16x16/places/distributor-logo.png

mint:
	ln -sf ../distributor-logos/mint.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/mint.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/mint.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/mint.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/mint.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/mint.png 16x16/places/distributor-logo.png

suse:
	ln -sf ../distributor-logos/suse.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/suse.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/suse.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/suse.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/suse.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/suse.png 16x16/places/distributor-logo.png

ubuntu:
	ln -sf ../distributor-logos/ubuntu.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/ubuntu.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/ubuntu.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/ubuntu.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/ubuntu.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/ubuntu.png 16x16/places/distributor-logo.png

xfce:
	ln -sf ../distributor-logos/xfce.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/xfce.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/xfce.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/xfce.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/xfce.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/xfce.png 16x16/places/distributor-logo.png

xubuntu:
	ln -sf ../distributor-logos/xubuntu.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/xubuntu.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/xubuntu.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/xubuntu.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/xubuntu.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/xubuntu.png 16x16/places/distributor-logo.png

zenwalk:
	ln -sf ../distributor-logos/zenwalk.svg scalable/places/start-here.svg
	ln -sf ../distributor-logos/zenwalk.svg scalable/places/gnome-main-menu.svg
	ln -sf ../distributor-logos/zenwalk.svg scalable/places/distributor-logo.svg
	ln -sf ../distributor-logos/zenwalk.png 16x16/places/start-here.png
	ln -sf ../distributor-logos/zenwalk.png 16x16/places/gnome-main-menu.png
	ln -sf ../distributor-logos/zenwalk.png 16x16/places/distributor-logo.png


_16x16: src/symlinks
	@echo creating 16x16 symlinks
	@test -d 16x16 || mkdir 16x16
	@test -d 16x16/actions || mkdir 16x16/actions
	@test -d 16x16/apps || mkdir 16x16/apps
	@test -d 16x16/categories || mkdir 16x16/categories
	@test -d 16x16/devices || mkdir 16x16/devices
	@test -d 16x16/distributor-logos || mkdir 16x16/distributor-logos
	@test -d 16x16/emblems || mkdir 16x16/emblems
	@test -d 16x16/mimetypes || mkdir 16x16/mimetypes
	@test -d 16x16/places || mkdir 16x16/places
	@test -d 16x16/status || mkdir 16x16/status
	@test -d 16x16/stock || mkdir 16x16/stock
	@sed -e 's/\.svg/\.png/g' -e 's/scalable/16x16/g' -e 's/^/ln -sf /g' <($(SYMLINKS)) | bash
	@find scalable/ -type f | sed -e 's/scalable\(.*\)svg/echo building 16x16\1png; rsvg-convert -w 16 -h 16 & > 16x16\1png/' | bash

_symlinks:
	@test -d scalable || ( echo "folder scalable doesn't exists"; false )
	@./symlink-file > src/symlinks

clean:
	@echo cleaning...
	@test -d scalable && rm -r scalable || true
	@test -d Clarity && rm -r Clarity || true
	@test -d 16x16 && rm -r 16x16 || true
	@bash -c 'for i in `find . -name "*-stamp"`; do rm $$i; done'

install-deb:
	mkdir debian/clarity-icon-theme/usr/share/icons/Clarity
	cp configure debian/clarity-icon-theme/usr/share/icons/Clarity
	cp index.theme debian/clarity-icon-theme/usr/share/icons/Clarity
	cp Makefile debian/clarity-icon-theme/usr/share/icons/Clarity
	cp build debian/clarity-icon-theme/usr/share/icons/Clarity
	cp README debian/clarity-icon-theme/usr/share/icons/Clarity
	cp change-theme debian/clarity-icon-theme/usr/share/icons/Clarity
	cp -r static debian/clarity-icon-theme/usr/share/icons/Clarity
	cp -r src debian/clarity-icon-theme/usr/share/icons/Clarity
	cp -r scalable debian/clarity-icon-theme/usr/share/icons/Clarity
	cp -r 16x16 debian/clarity-icon-theme/usr/share/icons/Clarity

source-deb:
	debuild -S

deb:
	dpkg-buildpackage -rfakeroot

tar.gz:
	@test -d Clarity && true || mkdir Clarity
	cp -r scalable Clarity/
	cp -r static Clarity/
	cp -r src Clarity/
	cp -r build Clarity/
	cp -r change-theme Clarity/
	cp -r configure Clarity/
	cp -r Makefile Clarity/
	cp -r index.theme Clarity/
	cp -r README Clarity/
	tar czvf ../${DIR}.tar.gz Clarity
	rm -r Clarity
