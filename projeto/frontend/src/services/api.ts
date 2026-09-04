// frontend/src/services/api.ts

// Define a URL base dependendo do ambiente
const API_BASE_URL = import.meta.env.PROD 
  ? "https://seu-dominio-php.com/backend/api" // URL da sua API PHP quando estiver em produção
  : "http://localhost/easyvacc-novo/backend/api"; // URL local para testes (caso use XAMPP/Laragon para debugar)

export const api = {
  async get(endpoint: string) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`);
    if (!response.ok) throw new Error("Erro na requisição GET");
    return response.json();
  },

  async post(endpoint: string, data: any) {
    const response = await fetch(`${API_BASE_URL}/${endpoint}`, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error("Erro na requisição POST");
    return response.json();
  }
};