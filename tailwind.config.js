/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                brandPrimario: '#003764',   // Pantone 2955 C - manual de marca oficial
                brandTexto: '#59595b',
                brandSecundario: '#C7A36E', // Pantone 465 M
            },
            fontFamily: {
                sans: ['Montserrat', 'system-ui', 'sans-serif'],
            },
        },
    },
    plugins: [],
};