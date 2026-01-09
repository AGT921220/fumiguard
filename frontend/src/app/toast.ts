import toast from 'react-hot-toast'
import type { ApiError } from './api'

export function showError(err: unknown, fallback = 'Ocurrió un error') {
  const maybe = err as Partial<ApiError> | undefined
  const msg =
    typeof maybe?.message === 'string' && maybe.message ? maybe.message : fallback
  toast.error(msg)
}

export function showSuccess(message: string) {
  toast.success(message)
}

