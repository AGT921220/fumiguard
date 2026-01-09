import { api } from '../../app/api'

export type ServicePlan = {
  id: number
  name: string
  is_active: boolean
  checklist_template?: unknown
  certificate_template?: unknown
}

export function listServicePlans() {
  return api<ServicePlan[]>('/api/v1/service-plans')
}

export function createServicePlan(data: Partial<ServicePlan> & { name: string }) {
  return api<ServicePlan>('/api/v1/service-plans', { method: 'POST', json: data })
}

export function deleteServicePlan(id: number) {
  return api<{ ok: boolean }>(`/api/v1/service-plans/${id}`, { method: 'DELETE' })
}

