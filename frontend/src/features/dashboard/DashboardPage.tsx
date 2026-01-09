import { useQuery } from '@tanstack/react-query'
import { api } from '../../app/api'
import { Card } from '../../components/Card'

export function DashboardPage() {
  const customers = useQuery({
    queryKey: ['customers'],
    queryFn: () => api<any[]>('/api/v1/customers'),
  })

  const plans = useQuery({
    queryKey: ['servicePlans'],
    queryFn: () => api<any[]>('/api/v1/service-plans'),
  })

  const today = new Date().toISOString().slice(0, 10)
  const agenda = useQuery({
    queryKey: ['agenda', today],
    queryFn: () => api<any[]>(`/api/v1/agenda?view=day&date=${today}`),
  })

  const kpis = [
    { label: 'Clientes', value: customers.data?.length ?? '—' },
    { label: 'Planes', value: plans.data?.length ?? '—' },
    { label: 'Citas hoy', value: agenda.data?.length ?? '—' },
  ]

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Dashboard</h1>
      <p className="mt-1 text-sm text-slate-600">KPIs básicos del tenant.</p>

      <div className="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        {kpis.map((k) => (
          <Card key={k.label}>
            <div className="text-xs font-semibold text-slate-500">{k.label}</div>
            <div className="mt-2 text-2xl font-bold">{k.value}</div>
          </Card>
        ))}
      </div>

      <div className="mt-4">
        <Card>
          <div className="text-sm font-semibold">Agenda (hoy)</div>
          <div className="mt-2 text-sm text-slate-600">
            {agenda.isLoading ? 'Cargando…' : null}
            {agenda.isError ? 'No se pudo cargar agenda.' : null}
            {!agenda.isLoading && !agenda.isError && agenda.data?.length === 0
              ? 'Sin citas.'
              : null}
          </div>
        </Card>
      </div>
    </div>
  )
}

