const colors = require('tailwindcss/colors')

module.exports = {
    theme: {
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.trueGray,
            indigo: colors.indigo,
            red: colors.rose,
            yellow: colors.amber,
            nav: {
                light: '#C8C5aC',
                original: '#F9F1E5',
                dark: '#e7c797',
            },
            textC: '#231F19',
            background: '#F7F1E9',
            btnBack: '#FFB60D',
            footer: '#F3E7D2',
            rouge: '#f22613',
            vert: '#249d00'

        },
        extend: {
            inset: {
                '1/6': '13%',
            }
        }
    }
}