const fs = require('fs');
const path = require('path');
const terser = require('terser');

const isProduction = process.env.NODE_ENV === 'production';
const force = process.argv.includes('--force');

// Get directory path from command line argument
const inputDir = process.argv[2];
if (!inputDir) {
  console.error('Usage: node minify-js.js <directory-path>');
  process.exit(1);
}

// Resolve to absolute path
const JS_DIR = path.resolve(inputDir);

console.log(`🔧 Minifying JS files in ${JS_DIR} (${isProduction ? 'production' : 'development'} mode. Forced: ${force})`);

async function minifyFile(filePath) {
  const minPath = path.join(
    path.dirname(filePath),
    path.basename(filePath, '.js') + '.min.js'
  );

  const srcStat = fs.statSync(filePath);
  const minExists = fs.existsSync(minPath);

  // Skip files that are already up to date
  if (!force && minExists) {
    const minStat = fs.statSync(minPath);
    if (minStat.mtime >= srcStat.mtime) {
      console.log(`✓ Skipping ${path.basename(filePath)} (up to date)`);
      return;
    }
  }

  const code = fs.readFileSync(filePath, 'utf8');

  const result = await terser.minify(code, {
    compress: isProduction ? {drop_console: ['log']} : true,
    mangle: true,
    format: {
      // Keep only license comments
      comments: /@license|@preserve|copyright/i
    },
    sourceMap: isProduction
      ? false
      : {
          filename: path.basename(minPath),
          url: path.basename(minPath) + '.map'
        }
  });

  // Write minified JS
  fs.writeFileSync(minPath, result.code);

  // Write source map only in development
  if (!isProduction && result.map) {
    fs.writeFileSync(minPath + '.map', result.map);
  }

  console.log(`✔ Minified ${path.relative(JS_DIR, filePath)}`);
}

// Recursively walk JS directory
async function walk(dir) {
  const files = fs.readdirSync(dir);

  for (const file of files) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory()) {
      await walk(fullPath);
    } else if (file.endsWith('.js') && !file.endsWith('.min.js')) {
      await minifyFile(fullPath);
    }
  }
}

// Delete all .map files in JS_DIR (for production)
function cleanSourceMaps(dir) {
  const files = fs.readdirSync(dir);

  for (const file of files) {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory()) {
      cleanSourceMaps(fullPath);
    } else if (file.endsWith('.map')) {
      fs.unlinkSync(fullPath);
      console.log(`🗑 Deleted ${path.relative(JS_DIR, fullPath)}`);
    }
  }
}

// Run
(async function () {
  if (isProduction) {
    console.log('🚀 Production build: deleting existing .map files');
    cleanSourceMaps(JS_DIR);
  }
  await walk(JS_DIR);
})();