import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { useState } from 'react'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import { createServicePlan, deleteServicePlan, listServicePlans } from './api'

export function ServicePlansPage() {
  const qc = useQueryClient()
  const { isReadOnly, setReadOnly } = useReadOnly()
  const [name, setName] = useState('')

  const plans = useQuery({
    queryKey: ['servicePlans'],
    queryFn: listServicePlans,
  })

  const createMut = useMutation({
    mutationFn: createServicePlan,
    onSuccess: async () => {
      setName('')
      showSuccess('Plan creado')
      await qc.invalidateQueries({ queryKey: ['servicePlans'] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo crear el plan')
    },
  })

  const delMut = useMutation({
    mutationFn: deleteServicePlan,
    onSuccess: async () => {
      showSuccess('Plan eliminado')
      await qc.invalidateQueries({ queryKey: ['servicePlans'] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo eliminar el plan')
    },
  })

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Service Plans</h1>
      <p className="mt-1 text-sm text-slate-600">Plantillas de checklist y certificado.</p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">Nuevo plan</div>
          <div className="mt-3 flex gap-2">
            <Input value={name} onChange={(e) => setName(e.target.value)} placeholder="Nombre" />
            <Button
              disabled={isReadOnly || !name || createMut.isPending}
              onClick={() => createMut.mutate({ name })}
            >
              Crear
            </Button>
          </div>
        </Card>

        <Card>
          <div className="flex items-center justify-between">
            <div className="text-sm font-semibold">Listado</div>
            <div className="text-xs text-slate-500">
              {plans.isLoading ? 'Cargando…' : `${plans.data?.length ?? 0} planes`}
            </div>
          </div>
          <div className="mt-3 divide-y divide-slate-100">
            {(plans.data ?? []).map((p) => (
              <div key={p.id} className="flex items-center justify-between py-3">
                <div>
                  <div className="text-sm font-semibold">{p.name}</div>
                  <div className="text-xs text-slate-500">
                    {p.is_active ? 'Activo' : 'Inactivo'}
                  </div>
                </div>
                <Button
                  className="bg-slate-100 text-slate-900"
                  disabled={isReadOnly || delMut.isPending}
                  onClick={() => delMut.mutate(p.id)}
                >
                  Eliminar
                </Button>
              </div>
            ))}
            {!plans.isLoading && (plans.data?.length ?? 0) === 0 ? (
              <div className="py-6 text-sm text-slate-600">Sin planes.</div>
            ) : null}
          </div>
        </Card>
      </div>
    </div>
  )
}

