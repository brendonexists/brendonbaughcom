/** @type {import('tailwindcss').Config} */
const typography = require('@tailwindcss/typography');
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    "./**/*.php",
    "./template-parts/**/*.php",
    "./inc/**/*.php"
  ],
  theme: {
    fontFamily: {
      sans: ['Inter', ...defaultTheme.fontFamily.sans],
      display: ['"League Spartan"', ...defaultTheme.fontFamily.sans],
    },
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      inherit: 'inherit',
      white: 'var(--c-surface)',
      black: 'var(--c-text)',
      canvas: 'var(--c-canvas)',
      surface: 'var(--c-surface)',
      'surface-2': 'var(--c-surface-2)',
      text: 'var(--c-text)',
      muted: 'var(--c-muted)',
      border: 'var(--c-border)',
      primary: 'rgb(var(--c-primary-rgb) / <alpha-value>)',
      'primary-hover': 'rgb(var(--c-primary-hover-rgb) / <alpha-value>)',
      danger: 'rgb(var(--c-danger-rgb) / <alpha-value>)',
      focus: 'var(--c-focus)',
      slate: {
        50: 'var(--c-surface)',
        100: 'var(--c-surface-2)',
        200: 'var(--c-border)',
        300: 'rgb(var(--c-text-rgb) / 0.35)',
        400: 'rgb(var(--c-text-rgb) / 0.55)',
        500: 'var(--c-muted)',
        600: 'var(--c-muted)',
        700: 'rgb(var(--c-text-rgb) / 0.85)',
        800: 'var(--c-text)',
        900: 'rgb(var(--c-text-rgb) / <alpha-value>)',
        950: 'var(--c-text)',
      },
      gray: {
        300: 'var(--c-border)',
        900: 'var(--c-text)',
      },
    },
    extend: {
      typography: (theme) => ({
        DEFAULT: {
          css: {
            color: 'var(--c-muted)',
            lineHeight: '1.9',
            strong: {
              color: 'var(--c-text)',
            },
            a: {
              color: 'var(--c-primary)',
              fontWeight: '600',
              transition: 'color 0.2s ease',
              '&:hover': {
                color: 'var(--c-primary-hover)',
              },
            },
            h1: {
              color: 'var(--c-text)',
              fontWeight: '700',
              letterSpacing: '0.01em',
            },
            h2: {
              color: 'var(--c-text)',
              fontWeight: '700',
              letterSpacing: '0.015em',
              paddingBottom: theme('spacing.2'),
              borderBottom: '2px solid var(--c-border)',
              marginTop: theme('spacing.8'),
            },
            h3: {
              color: 'var(--c-text)',
              fontWeight: '600',
              marginTop: theme('spacing.6'),
            },
            p: {
              marginTop: theme('spacing.3'),
            },
            blockquote: {
              borderLeftColor: 'var(--c-border)',
              backgroundColor: 'var(--c-surface-2)',
              color: 'var(--c-text)',
              paddingLeft: theme('spacing.4'),
              paddingRight: theme('spacing.4'),
            },
            code: {
              backgroundColor: 'var(--c-surface-2)',
              color: 'var(--c-text)',
              borderRadius: theme('borderRadius.md'),
              padding: '0.1rem 0.25rem',
            },
            'ol li::marker': {
              color: 'var(--c-primary)',
            },
            'ul li::marker': {
              color: 'var(--c-primary)',
            },
            hr: {
              borderColor: 'var(--c-border)',
              opacity: '1',
              marginTop: theme('spacing.10'),
              marginBottom: theme('spacing.10'),
            },
          },
        },
      }),
    },
  },
  plugins: [typography],
};
