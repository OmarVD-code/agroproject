<template>
  <b-modal
    title="REGISTRAR CLIENTE"
    v-model="show"
    @hidden="onCancel"
    size="xmd"
    no-close-on-backdrop
    no-close-on-esc
  >
    <validation-observer ref="formCustomer">
      <b-row>
        <b-col cols="12">
          <validation-provider name="name" rules="required" v-slot="{ errors }">
            <b-form-group label="Nombre" label-for="name">
              <b-form-input
                id="name"
                v-model="customer.name"
                required
                autofocus
                @keyup.enter="onOk"
                :class="errors.length > 0 ? 'border-danger' : ''"
              />
            </b-form-group>
          </validation-provider>
        </b-col>
      </b-row>
    </validation-observer>
    <template #modal-footer>
      <b-button variant="primary" @click="onOk">{{
        isEdit ? "Editar" : "Registrar"
      }}</b-button>
    </template>
  </b-modal>
</template>

<script>
export default {
  props: {
    customerToEdit: {
      type: Object,
      default: null,
      required: false,
    },
  },
  data() {
    return {
      customer: {
        name: "",
        phone: "",
        address: "",
        dni: "",
      },
      show: false,
    };
  },
  created() {
    this.show = true;
    if (this.isEdit) {
      this.customer = { ...this.customerToEdit };
    }
  },
  computed: {
    isEdit() {
      return this.customerToEdit !== null;
    },
  },
  methods: {
    async onOk() {
      if (this.isEdit) {
        await this.editCustomer();
      } else {
        await this.addCustomer();
      }
    },
    async addCustomer() {
      try {
        this.hideValidator = true;
        const validation = await this.$refs.formCustomer.validate();
        if (!validation) {
          return;
        }
        const data = await axios.post("/customers-add", this.customer);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload");
        } else {
          this.$toastr.error("Ha ocurrido un error al registrar el cliente");
        }
      } catch (error) {
        console.log(error);
      }
    },
    async editCustomer() {
      try {
        this.hideValidator = true;
        const validation = await this.$refs.formCustomer.validate();
        if (!validation) {
          return;
        }
        const data = await axios.post("/customers-edit", this.customer);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload");
        } else {
          this.$toastr.error("Ha ocurrido un error al actualizar el cliente");
        }
      } catch (error) {
        console.log(error);
      }
    },
    onCancel() {
      this.show = false;
      this.$emit("close");
    },
  },
};
</script>