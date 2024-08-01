# Installing Node on Windows

## installs fnm (Fast Node Manager)

```bash
winget install Schniz.fnm
```

## download and install Node.js

```bash
fnm use --install-if-missing 20
```

## verifies the right Node.js version is in the environment

```bash
node -v # should print `v20.14.0`
```

## verifies the right NPM version is in the environment

```bash
npm -v # should print `10.7.0`
```
