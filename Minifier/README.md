# Minifier

## Usage

Minify JavaScript files using Node.js and Terser. Place this directory inside the child theme folder at the same level as the /js directory you wish to minify. Running the following command will loop through the .js files and create or update a .min.js file as well as a .map file for development purposes

```bash
npm run dev
```

Running the following command will remove the .map files for production and minimize the js files if the files have been updated since the last run.

## Example

```bash
npm run prod
```

## Setup

1. Run `npm install` in the `Minifier` folder to install dependencies.
2. Use one of the command above to minify files.
