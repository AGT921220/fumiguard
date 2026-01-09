import { env } from './env'
import { getToken } from './storage'

export type ApiErrorShape = {
  message: string
  errors: Record<string, unknown> | object
  trace_id: string
}

export class ApiError extends Error {
  status: number
  traceId?: string
  details?: ApiErrorShape

  constructor(status: number, message: string, details?: ApiErrorShape) {
    super(message)
    this.status = status
    this.details = details
    this.traceId = details?.trace_id
  }
}

type Json = Record<string, unknown> | unknown[] | string | number | boolean | null

async function parseJsonSafe(res: Response): Promise<Json | null> {
  const text = await res.text()
  if (!text) return null
  try {
    return JSON.parse(text) as Json
  } catch {
    return null
  }
}

export async function api<T>(
  path: string,
  init: RequestInit & { json?: unknown } = {},
): Promise<T> {
  const url = `${env.apiBaseUrl}${path.startsWith('/') ? '' : '/'}${path}`

  const headers = new Headers(init.headers)
  headers.set('Accept', 'application/json')

  const token = getToken()
  if (token) headers.set('Authorization', `Bearer ${token}`)

  let body: BodyInit | undefined = init.body as BodyInit | undefined
  if (init.json !== undefined) {
    headers.set('Content-Type', 'application/json')
    body = JSON.stringify(init.json)
  }

  const res = await fetch(url, { ...init, headers, body })
  if (res.ok) {
    const data = (await parseJsonSafe(res)) as T | null
    return (data ?? (null as unknown as T)) as T
  }

  const data = (await parseJsonSafe(res)) as ApiErrorShape | null
  const message = data?.message || `Error HTTP ${res.status}`
  throw new ApiError(res.status, message, data ?? undefined)
}

export async function apiBlob(
  path: string,
  init: RequestInit = {},
): Promise<{ blob: Blob; filename?: string }> {
  const url = `${env.apiBaseUrl}${path.startsWith('/') ? '' : '/'}${path}`

  const headers = new Headers(init.headers)
  const token = getToken()
  if (token) headers.set('Authorization', `Bearer ${token}`)

  const res = await fetch(url, { ...init, headers })
  if (!res.ok) {
    const data = (await parseJsonSafe(res)) as ApiErrorShape | null
    const message = data?.message || `Error HTTP ${res.status}`
    throw new ApiError(res.status, message, data ?? undefined)
  }

  const blob = await res.blob()
  const cd = res.headers.get('content-disposition') || ''
  const match = /filename="([^"]+)"/.exec(cd)
  return { blob, filename: match?.[1] }
}

