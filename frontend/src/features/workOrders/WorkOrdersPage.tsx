import { useMutation } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import { appointmentToWorkOrder } from './api'

export function WorkOrdersPage() {
  const navigate = useNavigate()
  const { isReadOnly, setReadOnly } = useReadOnly()
  const [appointmentId, setAppointmentId] = useState('')

  const create = useMutation({
    mutationFn: (id: number) => appointmentToWorkOrder(id),
    onSuccess: (wo) => {
      showSuccess(`WorkOrder #${wo.id} creada`)
      navigate(`/executor/${wo.id}`)
    },
    onError: (e) => {
      const err = e as ApiError
      if (err.status === 403) setReadOnly(true)
      showError(e, 'No se pudo crear la orden')
    },
  })

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Work Orders</h1>
      <p className="mt-1 text-sm text-slate-600">
        Crear una orden desde un appointment y abrir el ejecutor móvil.
      </p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">Appointment → WorkOrder</div>
          <div className="mt-3 flex gap-2">
            <Input
              inputMode="numeric"
              value={appointmentId}
              onChange={(e) => setAppointmentId(e.target.value)}
              placeholder="Appointment ID"
            />
            <Button
              disabled={isReadOnly || !appointmentId || create.isPending}
              onClick={() => create.mutate(Number(appointmentId))}
            >
              Crear
            </Button>
          </div>
          <div className="mt-2 text-xs text-slate-500">
            Tip: crea la cita en Agenda y usa su ID aquí.
          </div>
        </Card>
      </div>
    </div>
  )
}

