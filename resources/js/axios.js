import axios from "axios";

const csrfToken = document
  .querySelector('meta[name="csrf-token"]')
  .getAttribute("content");

const instance = axios.create({
  headers: {
    "Content-Type": "application/json",
    "X-CSRF-TOKEN": csrfToken,
  },
});

export default instance;