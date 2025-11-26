import { Component, Inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatCheckboxModule } from '@angular/material/checkbox';
import { Room } from '../../services/room';

export interface DialogData {
    mode: 'create' | 'edit';
    room?: Room;
}

@Component({
    selector: 'app-room-form-dialog',
    standalone: true,
    imports: [
        CommonModule,
        ReactiveFormsModule,
        MatDialogModule,
        MatFormFieldModule,
        MatInputModule,
        MatButtonModule,
        MatSelectModule,
        MatCheckboxModule
    ],
    templateUrl: './room-form-dialog.html',
    styleUrls: ['./room-form-dialog.css']
})
export class RoomFormDialogComponent {
    roomForm: FormGroup;
    mode: 'create' | 'edit';

    roomTypes = ['Suite', 'Deluxe', 'Estándar', 'Familiar', 'Ejecutiva'];

    constructor(
        private fb: FormBuilder,
        public dialogRef: MatDialogRef<RoomFormDialogComponent>,
        @Inject(MAT_DIALOG_DATA) public data: DialogData
    ) {
        this.mode = data.mode;

        this.roomForm = this.fb.group({
            name: [data.room?.name || '', [Validators.required, Validators.minLength(3)]],
            description: [data.room?.description || '', [Validators.required]],
            type: [data.room?.type || '', [Validators.required]],
            price: [data.room?.price || 0, [Validators.required, Validators.min(0)]],
            capacity: [data.room?.capacity || 1, [Validators.required, Validators.min(1)]],
            image_url: [data.room?.image_url || ''],
            is_available: [data.room?.is_available !== false]
        });
    }

    onCancel(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        if (this.roomForm.valid) {
            this.dialogRef.close(this.roomForm.value);
        } else {
            Object.keys(this.roomForm.controls).forEach(key => {
                this.roomForm.get(key)?.markAsTouched();
            });
        }
    }

    get title(): string {
        return this.mode === 'create' ? 'Crear Nueva Habitación' : 'Editar Habitación';
    }

    get submitButtonText(): string {
        return this.mode === 'create' ? 'Crear Habitación' : 'Guardar Cambios';
    }
}
