// frontend/src/services/api.ts

// URL fixa diretamente para o backend no Render
const API_BASE_URL = "https://easyvacc-1.onrender.com/api";

export const api = {
  async get(endpoint: string) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`);
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.erro || "Erro na requisição GET");
    }
    return response.json();
  },

  async post(endpoint: string, data: any) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    const result = await response.json();
    if (!response.ok) {
      throw new Error(result.erro || "Erro na requisição POST");
    }
    return result;
  }
};