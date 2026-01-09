import { api } from '../../app/api'

export type AgendaItem = {
  id: number
  customer_id: number
  site_id: number
  scheduled_at: string
  status: string
}

export function listAgenda(view: 'day' | 'week', date: string) {
  return api<AgendaItem[]>(`/api/v1/agenda?view=${view}&date=${date}`)
}

export function createAppointment(payload: {
  customer_id: number
  site_id: number
  service_plan_id?: number | null
  scheduled_at: string
  notes?: string | null
}) {
  return api<{ id: number }>('/api/v1/appointments', { method: 'POST', json: payload })
}

