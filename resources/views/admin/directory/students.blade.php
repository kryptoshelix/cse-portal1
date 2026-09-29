@extends('layouts.admin')
@section('title','Students')
@php $back = ['route'=>'admin.students.index','label'=>'Student directory']; $backType='students'; @endphp
@include('admin.directory._index', ['people'=>$students, 'createRoute'=>route('admin.students.create'), 'newLabel'=>'Add student'])
