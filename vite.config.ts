import { defineConfig } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import fs from 'node:fs/promises'

const now = Date.now()

export default defineConfig({
    root: 'src/frontend',
    build: {
        outDir: '../../assets/build',
        manifest: true,
        minify: 'terser',
        lib: {
            formats: ['es'],
            entry: {
                global: 'global/global.ts',
                adminWoocommerce: 'admin/admin.ts',
            },
            name: 'plantae-lukavec-frontend',
            fileName: (format, entryName) =>
                `plantae-lukavec-frontend-${entryName}-${now}`,
        },
    },
    define: {
        'process.env': {},
    },
    plugins: [
        tailwindcss(),
        {
            name: 'custom',
            async writeBundle() {
                await fs.writeFile(
                    './assets/build/.vite/build-time',
                    String(now)
                )
            },
        },
    ],
})
