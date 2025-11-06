import js from '@eslint/js';
import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';
import vueParser from 'vue-eslint-parser';

export default [
  js.configs.recommended,
  ...vue.configs['flat/recommended'],
  prettier,
  {
    ignores: ['dist/**', 'node_modules/**'],
  },
  {
    files: ['**/*.vue', '**/*.js'],
    languageOptions: {
      parser: vueParser,
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        console: 'readonly',
        window: 'readonly',
        document: 'readonly',
        localStorage: 'readonly',
      },
    },
    rules: {
      'vue/multi-word-component-names': 'off',
      'no-console': 'warn',
    },
  },
];

