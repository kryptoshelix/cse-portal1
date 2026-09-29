@extends('layouts.admin')
@section('title','Faculty')
@php $back = ['route'=>'admin.faculty.index','label'=>'Faculty directory']; $backType='faculty'; @endphp
@include('admin.directory._index', ['people'=>$faculty, 'createRoute'=>route('admin.faculty.create'), 'newLabel'=>'Add faculty'])
