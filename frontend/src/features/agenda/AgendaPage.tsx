import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useMemo, useState } from 'react'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import { listCustomers, listSites } from '../customers/api'
import { listServicePlans } from '../servicePlans/api'
import { createAppointment, listAgenda } from './api'

function todayISO() {
  return new Date().toISOString().slice(0, 10)
}

export function AgendaPage() {
  const qc = useQueryClient()
  const { isReadOnly, setReadOnly } = useReadOnly()

  const [view, setView] = useState<'day' | 'week'>('day')
  const [date, setDate] = useState(todayISO())

  const agenda = useQuery({
    queryKey: ['agenda', view, date],
    queryFn: () => listAgenda(view, date),
  })

  const customers = useQuery({ queryKey: ['customers'], queryFn: listCustomers })
  const [customerId, setCustomerId] = useState<number | null>(null)

  const sites = useQuery({
    queryKey: ['sites', customerId],
    queryFn: () => listSites(customerId as number),
    enabled: customerId !== null,
  })

  const plans = useQuery({ queryKey: ['servicePlans'], queryFn: listServicePlans })

  const [siteId, setSiteId] = useState<number | null>(null)
  const [planId, setPlanId] = useState<number | null>(null)
  const [scheduledAt, setScheduledAt] = useState(() => new Date().toISOString())

  const canCreate = useMemo(
    () => customerId !== null && siteId !== null && scheduledAt,
    [customerId, siteId, scheduledAt],
  )

  const createMut = useMutation({
    mutationFn: createAppointment,
    onSuccess: async () => {
      showSuccess('Cita creada')
      await qc.invalidateQueries({ queryKey: ['agenda'] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo crear la cita')
    },
  })

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Agenda</h1>
      <p className="mt-1 text-sm text-slate-600">Vista día/semana.</p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="flex gap-2">
            <button
              className={`rounded-lg px-3 py-2 text-sm ${view === 'day' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-900'}`}
              onClick={() => setView('day')}
            >
              Día
            </button>
            <button
              className={`rounded-lg px-3 py-2 text-sm ${view === 'week' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-900'}`}
              onClick={() => setView('week')}
            >
              Semana
            </button>
            <div className="flex-1" />
            <Input type="date" value={date} onChange={(e) => setDate(e.target.value)} />
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">Crear cita</div>
          <div className="mt-3 grid gap-2">
            <select
              className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={customerId ?? ''}
              onChange={(e) => {
                const v = e.target.value ? Number(e.target.value) : null
                setCustomerId(v)
                setSiteId(null)
              }}
              disabled={isReadOnly}
            >
              <option value="">Customer…</option>
              {(customers.data ?? []).map((c) => (
                <option key={c.id} value={c.id}>
                  {c.name}
                </option>
              ))}
            </select>

            <select
              className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={siteId ?? ''}
              onChange={(e) => setSiteId(e.target.value ? Number(e.target.value) : null)}
              disabled={isReadOnly || !customerId}
            >
              <option value="">Site…</option>
              {(sites.data ?? []).map((s) => (
                <option key={s.id} value={s.id}>
                  {s.name}
                </option>
              ))}
            </select>

            <select
              className="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm"
              value={planId ?? ''}
              onChange={(e) => setPlanId(e.target.value ? Number(e.target.value) : null)}
              disabled={isReadOnly}
            >
              <option value="">Service plan (opcional)…</option>
              {(plans.data ?? []).map((p) => (
                <option key={p.id} value={p.id}>
                  {p.name}
                </option>
              ))}
            </select>

            <Input
              type="datetime-local"
              value={scheduledAt.slice(0, 16)}
              onChange={(e) => setScheduledAt(new Date(e.target.value).toISOString())}
              disabled={isReadOnly}
            />

            <Button
              disabled={isReadOnly || !canCreate || createMut.isPending}
              onClick={() =>
                createMut.mutate({
                  customer_id: customerId as number,
                  site_id: siteId as number,
                  service_plan_id: planId,
                  scheduled_at: scheduledAt,
                })
              }
            >
              Crear cita
            </Button>
          </div>
        </Card>

        <Card>
          <div className="flex items-center justify-between">
            <div className="text-sm font-semibold">Citas</div>
            <div className="text-xs text-slate-500">
              {agenda.isLoading ? 'Cargando…' : `${agenda.data?.length ?? 0} items`}
            </div>
          </div>
          <div className="mt-3 divide-y divide-slate-100">
            {(agenda.data ?? []).map((a) => (
              <div key={a.id} className="py-3">
                <div className="text-sm font-semibold">Appointment #{a.id}</div>
                <div className="text-xs text-slate-500">
                  {a.scheduled_at} · {a.status} · customer {a.customer_id} · site {a.site_id}
                </div>
              </div>
            ))}
            {!agenda.isLoading && (agenda.data?.length ?? 0) === 0 ? (
              <div className="py-6 text-sm text-slate-600">Sin citas.</div>
            ) : null}
          </div>
        </Card>
      </div>
    </div>
  )
}

