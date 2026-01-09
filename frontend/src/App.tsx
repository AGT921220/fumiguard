function App() {
  return (
    <div className="min-h-full bg-slate-50 text-slate-900">
      <div className="mx-auto flex max-w-3xl flex-col gap-6 px-6 py-16">
        <header className="space-y-2">
          <p className="text-sm font-semibold uppercase tracking-wide text-slate-500">
            SaaS Monorepo
          </p>
          <h1 className="text-3xl font-bold tracking-tight">
            Frontend (React + TypeScript)
          </h1>
          <p className="text-slate-600">
            Placeholder listo con Tailwind y React Query (QueryClient).
          </p>
        </header>

        <section className="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
          <h2 className="text-lg font-semibold">Siguiente paso</h2>
          <p className="mt-2 text-sm text-slate-600">
            Conecta aquí tus llamadas al backend (por ejemplo{' '}
            <code className="rounded bg-slate-100 px-1.5 py-0.5">
              /api/v1/health
            </code>
            ).
          </p>
        </section>
      </div>
    </div>
  )
}

export default App
