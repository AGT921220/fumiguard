import { useQuery } from '@tanstack/react-query'
import { api, ApiError } from '../../app/api'

export type BillingStatus = {
  status: string
  plan_key: string
  current_period_end?: string | null
}

/**
 * Nota: el backend actual no expone un endpoint de status.
 * Usamos una convención: si cualquier mutación falla con 403 "Suscripción inactiva",
 * la UI también entrará en modo read-only. Esta query se mantiene como placeholder
 * para cuando se exponga /api/v1/billing/status.
 */
export function useBillingStatus() {
  return useQuery({
    queryKey: ['billingStatus'],
    queryFn: async () => {
      try {
        return await api<BillingStatus>('/api/v1/billing/status')
      } catch (e) {
        const err = e as ApiError
        // 404 => backend no implementado todavía
        if (err.status === 404) return null
        throw e
      }
    },
    retry: false,
  })
}

