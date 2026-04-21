const js = require("@eslint/js");
const globals = require("globals");

module.exports = [
  {
    ignores: [
      "node_modules/**",
      "vendor/**",
      "public/assets/css/*.css",
      "public/assets/js/*.min.js",
      "public/assets/js/vendor/**",
    ],
  },
  js.configs.recommended,
  {
    files: ["public/assets/js/**/*.js", "resources/assets/js/**/*.js", "*.js"],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: "script",
      globals: {
        ...globals.browser,
        ...globals.node,
        bootstrap: "readonly",
      },
    },
    rules: {
      "no-console": "warn",
      "no-unused-vars": ["warn", { argsIgnorePattern: "^_" }],
    },
  },
];
