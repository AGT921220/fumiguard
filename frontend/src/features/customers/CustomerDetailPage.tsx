import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Link, useParams } from 'react-router-dom'
import { useState } from 'react'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import { createSite, listCustomers, listSites } from './api'

export function CustomerDetailPage() {
  const { id } = useParams()
  const customerId = Number(id)
  const qc = useQueryClient()
  const { isReadOnly, setReadOnly } = useReadOnly()
  const [siteName, setSiteName] = useState('')

  const customers = useQuery({
    queryKey: ['customers'],
    queryFn: listCustomers,
  })
  const customer = customers.data?.find((c) => c.id === customerId)

  const sites = useQuery({
    queryKey: ['sites', customerId],
    queryFn: () => listSites(customerId),
    enabled: Number.isFinite(customerId),
  })

  const createSiteMut = useMutation({
    mutationFn: createSite,
    onSuccess: async () => {
      setSiteName('')
      showSuccess('Sitio creado')
      await qc.invalidateQueries({ queryKey: ['sites', customerId] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo crear el sitio')
    },
  })

  if (!Number.isFinite(customerId)) {
    return (
      <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
        <Card>Customer inválido.</Card>
      </div>
    )
  }

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <div className="flex items-center justify-between">
        <div>
          <Link to="/customers" className="text-sm text-slate-600 hover:underline">
            ← Volver
          </Link>
          <h1 className="mt-2 text-xl font-bold tracking-tight">
            {customer?.name || 'Customer'}
          </h1>
          <p className="mt-1 text-sm text-slate-600">Sitios del cliente.</p>
        </div>
      </div>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">Nuevo sitio</div>
          <div className="mt-3 flex gap-2">
            <Input
              value={siteName}
              onChange={(e) => setSiteName(e.target.value)}
              placeholder="Nombre del sitio"
            />
            <Button
              disabled={isReadOnly || !siteName || createSiteMut.isPending}
              onClick={() => createSiteMut.mutate({ customer_id: customerId, name: siteName })}
            >
              Crear
            </Button>
          </div>
        </Card>

        <Card>
          <div className="flex items-center justify-between">
            <div className="text-sm font-semibold">Sitios</div>
            <div className="text-xs text-slate-500">
              {sites.isLoading ? 'Cargando…' : `${sites.data?.length ?? 0} sitios`}
            </div>
          </div>

          <div className="mt-3 divide-y divide-slate-100">
            {(sites.data ?? []).map((s) => (
              <div key={s.id} className="py-3">
                <div className="text-sm font-semibold">{s.name}</div>
                <div className="text-xs text-slate-500">
                  {s.city || '—'} {s.country ? `· ${s.country}` : ''}
                </div>
              </div>
            ))}
            {!sites.isLoading && (sites.data?.length ?? 0) === 0 ? (
              <div className="py-6 text-sm text-slate-600">Sin sitios.</div>
            ) : null}
          </div>
        </Card>
      </div>
    </div>
  )
}

