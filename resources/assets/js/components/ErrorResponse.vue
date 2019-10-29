<template>
    <div>
        <div class="modal-header">
            <h3 class="modal-title">An Error has Occurred</h3>
        </div>
        <div class="modal-body">
            <div class="col-lg-12">The application encountered a problem while processing your request and an error report has been submitted.</div>
            <!-- {{ message }}
            <div class="row form-group">
                <div class="col-lg-12">
                    <label>
                        If you would like to provide feed back about this error, please describe in detail what you were doing when the error arose.
                        <textarea v-model="feedback"></textarea>
                    </label>
                </div>
            </div> -->
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-6 text-right">
                    <div class="btn-group">
                        <button @click="$emit('close')" type="button" class="btn btn-default">
                            <i class="fa fa-fw fa-frown-o"></i>OK
                        </button>
                        <!--
                        <button @click="sendFeedback()" type="button" class="btn btn-default">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': isSending, 'fa-envelope': !isSending }"></i>
                        </button>
                        -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ErrorResponse',
    props: {
        message: {
          type: String
        },
        response: {
            type: Object,
        }
    },
    data: function () {
        return {
            isSending: false,
            feedback: ''
        }
    },
    methods: {
        sendFeedback: function () {

        }
    }

}
</script>

<style scoped>

</style>