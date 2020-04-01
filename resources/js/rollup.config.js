import vue from 'rollup-plugin-vue'

export default [
    {
        input: 'src/index.js',
        output: {
            format: 'esm',
            file: 'dist/library.esm.js'
        },
        plugins: [
            vue()
        ]
    },

    // Browser build.
    // {
    //     input: 'src/index.js',
    //     output: {
    //         format: 'iife',
    //         file: 'dist/library.js'
    //     },
    //     plugins: [
    //         vue()
    //     ]
    // }
]
