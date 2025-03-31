<script setup>
import { Head, router, useForm } from "@inertiajs/vue3";
import {ref, watch, computed } from "vue";
import { usePage } from "@inertiajs/vue3"; // Use Inertia's usePage to access URL info
import { useToast } from "primevue/usetoast";
import Card from "primevue/card";
import RadioButton from "primevue/radiobutton";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import FileUpload from "primevue/fileupload";
import Toast from "primevue/toast";

// Get current Inertia page props (URL)
const page = usePage();

const form = useForm({
    anonymous: true,
    subject: null,
    location: null,
    description: null,
    proposal: null,
    email: null,
    uploadedFiles: [],
    company_name: null,
    catastrophic_high_risk: false,
});

const toast = useToast();

const fileUploadRef = ref();

const search = (event) => {
    console.log('Complete event fired! ', event);
}

const onAdvancedUpload = (event) => {
    console.log('File uploaded', event);
    form.uploadedFiles = event.files;
}

const  clearForm = () => {
    form.clearErrors();
    form.reset();
    fileUploadRef.value.clear()
}

const submit = () => {
    form.clearErrors();
    const formData = form.data();

    if (!formData.email) delete formData.email;
    if (formData.uploadedFiles.length === 0) delete formData.uploadedFiles;

    router.post(route('report.store'), formData, {
        forceFormData: true,
        onSuccess: (res) => {
            clearForm();

            toast.add({
                severity: 'success',
                summary: 'Success!',
                detail: 'Report has been placed successfully!',
                life: 5000,
            })
        },
        onError: (err) => form.errors = err,
    })
}

watch(
    () => form.anonymous,
    (value) => !value && form.reset('email')
)

</script>

<template>
    <Head>
        <title>Safety Management System - POA – Hazard reporting</title>
    </Head>

    <Toast style="max-width: 100vw"/>

    <div class="flex min-h-screen py-5 justify-center items-center bg-stone-100">
        <Card>
            <template #title>
                <p>
                    Safety management system -
                </p>
                <p> POA – Hazard reporting</p>
            </template>
            <template #subtitle>Fill the form</template>
            <template #content>
                <div class="flex flex-col flex-wrap gap-4">
                    <div class="flex gap-12 flex-col">
                        <div class="flex gap-8">
                            <div class="flex items-center">
                                <RadioButton v-model="form.anonymous" inputId="anonymous" name="anonymously" :value="true"/>
                                <label for="anonymous" class="ml-3 cursor-pointer">Anonymously</label>
                            </div>

                            <div class="flex items-center">
                                <RadioButton v-model="form.anonymous" inputId="non-anonymous" name="non-anonymous"
                                             :value="false"/>
                                <label for="non-anonymous" class="ml-3 cursor-pointer">Non-Anonymously</label>
                            </div>
                        </div>

                        <!-- New row for "Catastrophic - High RISK" selection -->
                        <div class="flex">
                            <div class="flex flex-col w-full">
                                <label class="mb-2" for="catastrophic_high_risk" :class="{ 'p-error': form.errors.catastrophic_high_risk }">Catastrophic - High RISK: *</label>
                                <div class="flex gap-8">
                                    <div class="flex items-center">
                                        <RadioButton v-model="form.catastrophic_high_risk" inputId="catastrophic_yes" name="catastrophic_high_risk" :value="true"/>
                                        <label for="catastrophic_yes" class="ml-3 cursor-pointer">Yes</label>
                                    </div>

                                    <div class="flex items-center">
                                        <RadioButton v-model="form.catastrophic_high_risk" inputId="catastrophic_no" name="catastrophic_high_risk" :value="false"/>
                                        <label for="catastrophic_no" class="ml-3 cursor-pointer">No</label>
                                    </div>
                                </div>
                                <small v-if="form.errors.catastrophic_high_risk" class="p-error">{{ form.errors.catastrophic_high_risk }}</small>
                            </div>
                        </div>

                    </div>

                    <div class="flex ">
                        <div v-if="!form.anonymous" class="flex flex-col w-full">
                            <label for="email" :class="{ 'p-error': form.errors.email }">Email: *</label>
                            <InputText v-model="form.email" type="email" placeholder="Enter your email..." :class="{ 'p-invalid': form.errors.email }"/>
                            <small v-if="form.errors.email" class="p-error">{{ form.errors.email }}</small>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <label for="subject" :class="{ 'p-error': form.errors.subject }">Subject/Aircraft Reg./Project: *</label>
                            <InputText v-model="form.subject"
                                       id="subject"
                                       type="text"
                                       class="w-full"
                                       placeholder="Subject / Aircraft Reg. / Project ..."
                                       :class="{ 'p-invalid': form.errors.subject }"
                            />
                            <small v-if="form.errors.subject" class="p-error">{{ form.errors.subject }}</small>
                        </div>
                    </div>

                    <!-- New row for "Company/Organization Name" -->
                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <label for="company_name" :class="{ 'p-error': form.errors.company_name }">Company/Organization Name: *</label>
                            <InputText v-model="form.company_name" id="company_name" type="text" class="w-full" placeholder="Enter the company/organization name..." :class="{ 'p-invalid': form.errors.company_name }"/>
                            <small v-if="form.errors.company_name" class="p-error">{{ form.errors.company_name }}</small>
                            <small v-else class="italic">e.g. Company XYZ</small>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <label for="location" :class="{ 'p-error': form.errors.location }">Location: *</label>
                            <InputText v-model="form.location"
                                       id="location"
                                       type="text"
                                       class="w-full"
                                       placeholder="Enter the location..."
                                       :class="{ 'p-invalid': form.errors.location }"
                                       />
<!--                            <Dropdown v-model="form.location" :options="locations" optionLabel="value" option-value="id"-->
<!--                                      placeholder="Select Location..." class="w-full md:w-14rem" :class="{ 'p-invalid': form.errors.location }"/>-->
                            <small v-if="form.errors.location" class="p-error">{{ form.errors.location }}</small>
                            <small v-else class="italic">e.g. Vilnius, Kaunas, Line</small>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <label for="description" :class="{ 'p-error': form.errors.description }">Report Description: *</label>
                            <Textarea id="description" v-model="form.description" rows="5" :class="{ 'p-invalid': form.errors.description }"/>
                            <small v-if="form.errors.description" class="p-error">{{ form.errors.description }}</small>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <label for="proposal" :class="{ 'p-error': form.errors.proposal }">Your Proposal for Preventions: *</label>
                            <Textarea id="proposal" v-model="form.proposal" rows="5" :class="{ 'p-invalid': form.errors.proposal }"/>
                            <small v-if="form.errors.proposal" class="p-error">{{ form.errors.proposal }}</small>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex flex-col w-full">
                            <FileUpload name="demo[]"
                                        @select="onAdvancedUpload($event)"
                                        :multiple="true"
                                        customUpload
                                        :show-upload-button="false"
                                        cancel-label="Clear"
                                        ref="fileUploadRef"
                                        accept="image/*,video/*,.pdf"
                                        :maxFileSize="52428800"
                                        :fileLimit="3"
                            >
                                <template #empty>
                                    <div class="flex flex-col items-center">
                                        <span class="pi pi-cloud-upload" style="font-size: 4rem;" />
                                        <p>Drag and drop files to here to upload</p>
                                        <small><i>IMG, VIDEO, PDF (max: 50MB)</i></small>
                                    </div>
                                </template>

<!--                                <template #content="slotProps">-->
<!--                                    <pre>{{ test(slotProps) }}</pre>-->

<!--                                </template>-->
                            </FileUpload>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <div class="flex gap-2 w-full justify-end">
                    <Button class="p-button-sm" label="Submit" @click="submit"/>
                    <Button class="p-button-sm" severity="secondary" label="Clear" @click="clearForm"/>
                </div>
            </template>
        </Card>
    </div>
</template>

<style lang="scss" scoped>
:deep(.p-inputtext) {
    width: 100%;
}

:deep(.p-fileupload-file-badge) {
    display: none;
}

</style>
