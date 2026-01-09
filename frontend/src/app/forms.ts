import { env } from './env'
import { getToken } from './storage'
import { ApiError } from './api'

export async function apiForm<T>(
  path: string,
  form: FormData,
  init: RequestInit = {},
): Promise<T> {
  const url = `${env.apiBaseUrl}${path.startsWith('/') ? '' : '/'}${path}`

  const headers = new Headers(init.headers)
  const token = getToken()
  if (token) headers.set('Authorization', `Bearer ${token}`)

  const res = await fetch(url, {
    ...init,
    method: init.method || 'POST',
    headers,
    body: form,
  })

  if (!res.ok) {
    let details: any = undefined
    try {
      details = await res.json()
    } catch {
      // ignore
    }
    throw new ApiError(res.status, details?.message || `Error HTTP ${res.status}`, details)
  }

  return (await res.json()) as T
}

