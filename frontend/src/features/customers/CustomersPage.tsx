import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import { createCustomer, deleteCustomer, listCustomers } from './api'
import { useState } from 'react'

export function CustomersPage() {
  const qc = useQueryClient()
  const { isReadOnly, setReadOnly } = useReadOnly()
  const [name, setName] = useState('')

  const customers = useQuery({
    queryKey: ['customers'],
    queryFn: listCustomers,
  })

  const createMut = useMutation({
    mutationFn: createCustomer,
    onSuccess: async () => {
      setName('')
      showSuccess('Cliente creado')
      await qc.invalidateQueries({ queryKey: ['customers'] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo crear el cliente')
    },
  })

  const delMut = useMutation({
    mutationFn: deleteCustomer,
    onSuccess: async () => {
      showSuccess('Cliente eliminado')
      await qc.invalidateQueries({ queryKey: ['customers'] })
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo eliminar el cliente')
    },
  })

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <div className="flex items-end justify-between gap-3">
        <div>
          <h1 className="text-xl font-bold tracking-tight">Customers</h1>
          <p className="mt-1 text-sm text-slate-600">Clientes y sus sitios.</p>
        </div>
      </div>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">Nuevo cliente</div>
          <div className="mt-3 flex gap-2">
            <Input
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="Nombre del cliente"
            />
            <Button
              disabled={isReadOnly || !name || createMut.isPending}
              onClick={() => createMut.mutate({ name })}
            >
              Crear
            </Button>
          </div>
          {isReadOnly ? (
            <div className="mt-2 text-xs text-slate-500">
              Solo lectura: no se permiten cambios.
            </div>
          ) : null}
        </Card>

        <Card>
          <div className="flex items-center justify-between">
            <div className="text-sm font-semibold">Listado</div>
            <div className="text-xs text-slate-500">
              {customers.isLoading ? 'Cargando…' : `${customers.data?.length ?? 0} clientes`}
            </div>
          </div>

          <div className="mt-3 divide-y divide-slate-100">
            {(customers.data ?? []).map((c) => (
              <div key={c.id} className="flex items-center justify-between py-3">
                <div>
                  <Link
                    to={`/customers/${c.id}`}
                    className="text-sm font-semibold text-slate-900 underline-offset-2 hover:underline"
                  >
                    {c.name}
                  </Link>
                  <div className="text-xs text-slate-500">{c.email || '—'}</div>
                </div>
                <Button
                  className="bg-slate-100 text-slate-900"
                  disabled={isReadOnly || delMut.isPending}
                  onClick={() => delMut.mutate(c.id)}
                >
                  Eliminar
                </Button>
              </div>
            ))}

            {!customers.isLoading && (customers.data?.length ?? 0) === 0 ? (
              <div className="py-6 text-sm text-slate-600">Sin clientes.</div>
            ) : null}
          </div>
        </Card>
      </div>
    </div>
  )
}

