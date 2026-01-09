import { api } from '../../app/api'

export type WorkOrder = {
  id: number
  appointment_id: number
  assigned_user_id?: number | null
  status: string
  check_in_at?: string | null
  check_out_at?: string | null
}

export function appointmentToWorkOrder(appointmentId: number) {
  return api<WorkOrder>(`/api/v1/appointments/${appointmentId}/work-order`, {
    method: 'POST',
  })
}

