// Minify a JavaScript or HTML file
// Usage: node minify.js <input.js|input.html> [output.min.js|output.min.html]
const fs = require('fs');
const terser = require('terser');
const htmlMinifier = require('html-minifier-terser');

const options = {
  compress: {
    drop_console: ['log'], // Remove console.log statements
    dead_code: false, // Don't remove unreachable code to avoid breaking functionality
    ecma: 2015, // Use ES6 syntax
    keep_fnames: true, // Keep function names for better debugging
    passes: 2 // Run multiple passes to improve compression
  }
};

const inputFile = process.argv[2];
const outputFile = process.argv[3] || (inputFile ? inputFile.replace(/\.(js|html)$/i, '.min.$1') : 'output.min.js');

if (!inputFile) {
  console.error('Usage: node minify.js <input.js|input.html> [output.min.js|output.min.html]');
  process.exit(1);
}

fs.readFile(inputFile, 'utf8', async (err, code) => {
  if (err) {
    console.error('Error reading input file:', err);
    process.exit(1);
  }
  try {
    if (inputFile.endsWith('.js')) {
      const result = await terser.minify(code, options);
      if (result.error) {
        console.error('Terser error:', result.error);
        process.exit(1);
      }
      fs.writeFile(outputFile, result.code, (err) => {
        if (err) {
          console.error('Error writing output file:', err);
          process.exit(1);
        }
        console.log(`Minified JS file written to ${outputFile}`);
      });
    } else if (inputFile.endsWith('.html')) {
      const minified = await htmlMinifier.minify(code, {
        collapseWhitespace: true,
        removeComments: true,
        minifyJS: true,
        minifyCSS: true
      });
      fs.writeFile(outputFile, minified, (err) => {
        if (err) {
          console.error('Error writing output file:', err);
          process.exit(1);
        }
        console.log(`Minified HTML file written to ${outputFile}`);
      });
    } else {
      console.error('Unsupported file type. Only .js and .html are supported.');
      process.exit(1);
    }
  } catch (e) {
    console.error('Minification failed:', e);
    process.exit(1);
  }
});
