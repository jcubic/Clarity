Name:           clarity-icons
Version:        %{_version}
Release:        %{_release}%{?dist}
Summary:        Clarity vector icon theme for GTK — CLI manager and icon sources
License:        CC-BY-SA-4.0
URL:            https://clarity.pl.eu.org
Source0:        %{name}-%{version}.tar.gz
BuildArch:      noarch
Requires:       bash coreutils findutils sed grep gawk curl

%description
Clean monoshape icons in nine predefined color themes plus community
themes. Includes the clarity CLI manager for switching variants,
installing community themes, and building icons.

%install
install -d %{buildroot}%{_bindir}
install -m 755 %{_srcdir}/bin/clarity %{buildroot}%{_bindir}/clarity
install -d %{buildroot}%{_datadir}/clarity-icons/src
cp -a %{_srcdir}/src/. %{buildroot}%{_datadir}/clarity-icons/src/
test -d %{_srcdir}/static && cp -a %{_srcdir}/static %{buildroot}%{_datadir}/clarity-icons/static || true
cp %{_srcdir}/index.theme %{buildroot}%{_datadir}/clarity-icons/index.theme
install -d %{buildroot}%{_datadir}/bash-completion/completions
install -m 644 %{_srcdir}/bin/clarity-completion.bash %{buildroot}%{_datadir}/bash-completion/completions/clarity
install -d %{buildroot}%{_datadir}/zsh/site-functions
install -m 644 %{_srcdir}/bin/_clarity %{buildroot}%{_datadir}/zsh/site-functions/_clarity

%files
%{_bindir}/clarity
%{_datadir}/clarity-icons/
%{_datadir}/bash-completion/completions/clarity
%{_datadir}/zsh/site-functions/_clarity
