import { api } from '../../app/api'
import { apiForm } from '../../app/forms'

export function checkIn(workOrderId: number, lat: number, lng: number) {
  return api(`/api/v1/work-orders/${workOrderId}/check-in`, {
    method: 'POST',
    json: { lat, lng },
  })
}

export function checkOut(workOrderId: number, lat: number, lng: number) {
  return api(`/api/v1/work-orders/${workOrderId}/check-out`, {
    method: 'POST',
    json: { lat, lng },
  })
}

export function startReport(workOrderId: number) {
  return api<{ id: number; work_order_id: number }>(
    `/api/v1/work-orders/${workOrderId}/report/start`,
    { method: 'POST' },
  )
}

export function saveChecklist(workOrderId: number, checklist: any[], notes?: string) {
  return api(`/api/v1/work-orders/${workOrderId}/report/checklist`, {
    method: 'PUT',
    json: { checklist, notes },
  })
}

export function addChemical(workOrderId: number, chemical_name: string, quantity: number, unit: string) {
  return api(`/api/v1/work-orders/${workOrderId}/report/chemicals`, {
    method: 'POST',
    json: { chemical_name, quantity, unit },
  })
}

export function uploadEvidence(workOrderId: number, file: File) {
  const form = new FormData()
  form.append('file', file)
  return apiForm(`/api/v1/work-orders/${workOrderId}/report/evidence`, form)
}

export function captureSignature(workOrderId: number, signed_by_name: string, signed_by_role: string | null, signature_data: string) {
  return api(`/api/v1/work-orders/${workOrderId}/report/signature`, {
    method: 'POST',
    json: { signed_by_name, signed_by_role, signature_data },
  })
}

export function finalize(workOrderId: number) {
  return api<{ id: number }>(`/api/v1/work-orders/${workOrderId}/report/finalize`, {
    method: 'POST',
  })
}

