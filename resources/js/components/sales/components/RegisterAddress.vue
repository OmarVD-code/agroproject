<template>
  <b-modal
    title="REGISTRAR DIRECCIÓN"
    v-model="show"
    @hidden="onCancel"
    size="xmd"
    no-close-on-backdrop
    no-close-on-esc
  >
    <validation-observer ref="formAddress">
      <b-row>
        <b-col cols="12">
          <validation-provider name="name" rules="required" v-slot="{ errors }">
            <b-form-group label="Nombre" label-for="name">
              <b-form-input
                id="name"
                v-model="address.name"
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
      <b-button variant="primary" @click="onOk">{{ "Registrar" }}</b-button>
    </template>
  </b-modal>
</template>

<script>
export default {
  data() {
    return {
      address: {
        name: "",
      },
      show: false,
    };
  },
  created() {
    this.show = true;
  },
  methods: {
    async onOk() {
      try {
        const validation = await this.$refs.formAddress.validate();
        if (!validation) {
          return;
        }
        const data = await axios.post("/address-add", this.address);

        if (data.status == 200) {
          this.show = false;
          this.$toastr.success(data.data.message, "Éxito");
          this.$emit("reload", data.data.id);
        } else {
          this.$toastr.error("Ha ocurrido un error al registrar el producto");
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